<?php
/**
 * Schema learning
 *
 * This file is part of XML-Schema-learner.
 *
 * XML-Schema-learner is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; version 3 of the
 * License.
 *
 * XML-Schema-learner is distributed in the hope that it will be
 * useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with XML-Schema-learner; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301 USA
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Basic converter for single occurence automatons to regular expressions
 *
 * CRX-Algorithm implemented like described in:
 *
 * "Inference of Concise DTDs from XML Data",
 * by
 *  - Geert Jan Bex
 *  - Frank Neven
 *  - Thomas Schwentick
 *  - Karl Tuyls
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slChareConverter extends slConverter
{
    /**
     * Array with nodes and their associated regular expressions
     * 
     * @var array
     */
    protected $nodes = array();

    /**
     * Equivalence classes found in the passed automaton.
     * 
     * @var array
     */
    protected $equivalenceClasses = array();

    /**
     * VConvert automaton to regular expression
     * 
     * @param slCountingSingleOccurenceAutomaton $automaton 
     * @return slRegularExpression
     */
    public function convertAutomaton( slSingleOccurenceAutomaton $automaton )
    {
        // Calculate equivalency classes
        $this->equivalenceClasses = array();
        $equivalence = $this->calculateEquivalencyAutomaton( $automaton );

        // Merge singleton nodes
        $this->mergeSingletonNodes( $equivalence );

        // Sort nodes topologically
        $nodes = $equivalence->getTopologicallySortedNodeList();

        // Build regular expression from sorted node sets
        return $this->buildRegularExpression( $automaton, $nodes );
    }

    /**
     * Calculate equivalent nodes
     *
     * Calculate groups of nodes, which are contained in their respective 
     * transitive reflexive closure in the automaton.
     *
     * Returns an automaton consisting of nodes, which each links to such a 
     * node group.
     * 
     * @param slCountingSingleOccurenceAutomaton $automaton 
     * @return void
     */
    protected function calculateEquivalencyAutomaton( slAutomaton $automaton )
    {
        $nodeValues = $automaton->getNodes();
        $nodes      = array_keys( $nodeValues );
        $nodeCount  = count( $nodes );
        $equivalent = array();
        $skip       = array();

        // Find equivalence classes in automaton
        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            if ( isset( $skip[$i] ) )
            {
                continue;
            }

            $this->equivalenceClasses[$nodes[$i]] = array( $nodes[$i] );
            $equivalent[$nodes[$i]] = $nodes[$i];
            for ( $j = $i + 1; $j < $nodeCount; ++$j )
            {
                if ( in_array( $nodes[$i], $automaton->transitiveClosure( $nodes[$j] ) ) &&
                     in_array( $nodes[$j], $automaton->transitiveClosure( $nodes[$i] ) ) )
                {
                    $this->equivalenceClasses[$nodes[$i]][] = $nodes[$j];
                    $equivalent[$nodes[$j]] = $nodes[$i];

                    // The mutal containment in eachs reflexive transitive 
                    // closure is obviously symetric
                    $skip[$j] = true;
                }
            }
        }

        // Readd edges between equivalency classes based on source automaton
        $equivalencyAutomaton = new slAutomaton();
        foreach ( $this->equivalenceClasses as $name => $nodes )
        {
            $equivalencyAutomaton->addNode( $name );

            foreach ( $nodes as $node )
            {
                foreach ( $automaton->getOutgoing( $node ) as $dst )
                {
                    if ( $name !== $equivalent[$dst] )
                    {
                        $equivalencyAutomaton->addEdge( $name, $equivalent[$dst] );
                    }
                }
            }
        }

        return $equivalencyAutomaton;
    }

    /**
     * Merge singleton nodes
     *
     * All equivalency classes which consist of just one nodes are considered 
     * singleton nodes. This method merges all maximum sets of singleton nodes, 
     * which share the same successors and precedessors.
     * 
     * @param slAutomaton $automaton 
     * @return void
     */
    protected function mergeSingletonNodes( slAutomaton $automaton )
    {
        $classes    = array_keys( $this->equivalenceClasses );
        $classCount = count( $classes );
        for ( $i = 0; $i < $classCount; ++$i )
        {
            if ( !isset( $this->equivalenceClasses[$classes[$i]] ) ||
                 count( $this->equivalenceClasses[$classes[$i]] ) > 1 )
            {
                // We only care for singletons
                continue;
            }

            for ( $j = $i + 1; $j < $classCount; ++$j )
            {
                if ( !isset( $this->equivalenceClasses[$classes[$j]] ) ||
                     count( $this->equivalenceClasses[$classes[$j]] ) > 1 )
                {
                    // We only care for singletons
                    continue;
                }

                if ( ( $automaton->getOutgoing( $classes[$i] ) === $automaton->getOutgoing( $classes[$j] ) ) &&
                     ( $automaton->getIncoming( $classes[$i] ) === $automaton->getIncoming( $classes[$j] ) ) )
                {
                    $this->equivalenceClasses[$classes[$i]][] = $classes[$j];
                    unset( $this->equivalenceClasses[$classes[$j]] );
                    $automaton->removeNode( $classes[$j] );
                }
            }
        }
    }

    /**
     * Wrap regular expression term in counting pattern
     *
     * Based on the occurences provided by the count array, the term should be 
     * wrapped in apropriate counting patterns.
     * 
     * @param array $counts 
     * @param slRegularExpression $term 
     * @return slRegularExpression
     */
    protected function wrapCountingPattern( array $counts, slRegularExpression $term )
    {
        switch ( true )
        {
            case ( $counts['min'] === 1 ) &&
                 ( $counts['max'] === 1 ):
                return $term;

            case ( $counts['min'] === 0 ) &&
                 ( $counts['max'] === 1 ):
                return new slRegularExpressionOptional( $term );

            case ( $counts['min'] === 0 ):
                return new slRegularExpressionRepeated( $term );

            default:
                return new slRegularExpressionRepeatedAtLeastOnce( $term );
        }
    }

    /**
     * Build regular expression
     *
     * Builds the regilar expression from the provided automaton, where each 
     * node associates with one of the equivalency classes, and a topologically 
     * sorted list of these nodes.
     *
     * The provided automaton must be an instance of the 
     * slCountingSingleOccurenceAutomaton, so t at it provides information on 
     * how often the token occur in the learned strings.
     * 
     * @param slCountingSingleOccurenceAutomaton $automaton 
     * @param array $classes 
     * @return slRegularExpression
     */
    protected function buildRegularExpression( slCountingSingleOccurenceAutomaton $automaton, array $classes )
    {
        $terms = array();
        $nodes = $automaton->getNodes();
        foreach ( $classes as $class )
        {
            $term = $classes = $this->equivalenceClasses[$class];
            if ( count( $term ) > 1 )
            {
                $term = new slRegularExpressionChoice( array_map(
                    function( $term ) use ( $nodes )
                    {
                        return new slRegularExpressionElement( $nodes[$term] );
                    },
                    $term 
                ) );
            }
            else
            {
                $term = new slRegularExpressionElement( $nodes[reset( $term )] );
            }

            $terms[] = $this->wrapCountingPattern(
                $automaton->getOccurenceSum( $classes ),
                $term
            );
        }

        return  new slRegularExpressionSequence( $terms );
    }
}

