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
 * REWRITE-Algorithm implemented like described in:
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
class slSoreConverter extends slConverter
{
    /**
     * Array with nodes and their associated regular expressions
     * 
     * @var array
     */
    protected $nodes = array();

    /**
     * VConvert automaton to regular expression
     * 
     * @param slSingleOccurenceAutomaton $automaton 
     * @return slRegularExpression
     */
    public function convertAutomaton( slSingleOccurenceAutomaton $automaton )
    {
        $automaton = clone $automaton;
        $nodes     = $automaton->getNodes();
        $states    = array_keys( $nodes );
        if ( ( $stateCount = count( $states ) ) < 1 )
        {
            return new slRegularExpressionEmpty();
        }

        $this->nodes = array();
        foreach ( $states as $state )
        {
            $this->nodes[$state] = new slRegularExpressionElement( $nodes[$state] );
        }

        $this->debugAutomaton( $automaton, $this->nodes, $i = 1, 'start' );
        do {
            $modification = false;
            
            $modification |= $this->disjunction( $automaton );
            $this->debugAutomaton( $automaton, $this->nodes, ++$i, 'disjunction' );
            $modification |= $this->concatenation( $automaton );
            $this->debugAutomaton( $automaton, $this->nodes, ++$i, 'concatenation' );
            $modification |= $this->selfLoop( $automaton );
            $this->debugAutomaton( $automaton, $this->nodes, ++$i, 'selfLoop' );
            $modification |= $this->optional( $automaton );
            $this->debugAutomaton( $automaton, $this->nodes, ++$i, 'optional' );
        } while ( $modification );

        if ( count( $this->nodes ) === 1 )
        {
            return reset( $this->nodes );
        }

        return false;
    }

    /**
     * Store a dot file representing the current automaton for debugging
     * 
     * @param slSingleOccurenceAutomaton $automaton 
     * @param array $regularExpressions 
     * @param int $counter 
     * @param string $label 
     * @return void
     */
    protected function debugAutomaton( slSingleOccurenceAutomaton $automaton, array $regularExpressions, $counter, $label )
    {
        // @codeCoverageIgnoreStart
        // This is pure debugging code, which is not required to be covered by 
        // unit tests.
        return;

        $fileName         = sprintf( 'debug/%04d_%s.dot', $counter, $label );
        $regExpVisitor    = new slRegularExpressionStringVisitor();
        $labels           = array_map( array( $regExpVisitor, 'visit' ), $regularExpressions );
        $automatonVisitor = new slAutomatonDotVisitor();

        file_put_contents(
            $fileName,
            $automatonVisitor->visit( $automaton, $labels )
        );
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get unique node name
     *
     * Get a new node name, which is not yet used in the graph
     * 
     * @return string
     */
    protected function getUniqueNodeName()
    {
        do {
            $name = substr( md5( microtime() ), 0, 8 );
        } while ( isset( $this->nodes[$name] ) );
        return $name;
    }

    /**
     * Disjuction rule
     *
     * Precondition: W = {r1, …, rn} is a set of states with n ≥ 2 such that 
     * every two nodes ri, rj have the same predecessor and successor set. 
     * (Note, that this implies that either (i) there are no edges in G 
     * between r1, …, rn at all or (ii) that, for each i, j there is an edge
     * (ri , rj ) in G∗ .)
     *
     * Action: Remove r1, …, rn, add a new node r = r1 + … + rn , redirect
     * all incoming and outgoing edges of r1, …, rn to r. In case of (ii)
     * add the edge (r, r).
     *
     * @param slSingleOccurenceAutomaton $automaton 
     * @return void
     */
    protected function disjunction( slSingleOccurenceAutomaton $automaton )
    {
        $nodeCount = count( $this->nodes );
        $nodeNames = array_keys( $this->nodes );
        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            $incoming = $automaton->getIncoming( $nodeNames[$i] );
            $outgoing = $automaton->getOutgoing( $nodeNames[$i] );

            for ( $j = $i + 1; $j < $nodeCount; ++$j )
            {
                // Precondition
                if ( ( $incoming !== $automaton->getIncoming( $nodeNames[$j] ) ) ||
                     ( $outgoing !== $automaton->getOutgoing( $nodeNames[$j] ) ) )
                {
                    continue;
                }

                // Find further nodes sharing the same properties
                $nodes = array( $nodeNames[$i], $nodeNames[$j] );
                for ( $k = $j + 1; $k < $nodeCount; ++$k )
                {
                    if ( ( $automaton->getIncoming( $nodeNames[$k] ) === $incoming ) &&
                         ( $automaton->getOutgoing( $nodeNames[$k] ) === $outgoing ) )
                    {
                        $nodes[] = $nodeNames[$k];
                    }
                }

                // Action: Merge nodes, if they share the same precedessors and 
                // successors.
                $choice  = array();
                foreach ( $nodes as $node )
                {
                    $choice[] = $this->nodes[$node];
                    unset( $this->nodes[$node] );
                    $automaton->removeNode( $node );
                }
                
                $this->nodes[$newNode = $this->getUniqueNodeName()] = new slRegularExpressionChoice( $choice );
                $automaton->addNode( $newNode );

                foreach ( $incoming as $node )
                {
                    $automaton->addEdge( $node, $newNode );
                }

                foreach ( $outgoing as $node )
                {
                    $automaton->addEdge( $newNode, $node );
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Concatenation rule
     *
     * Precondition: W = {r1, …, rn} is a maximal set of states, n ≥ 2,
     * such that there is an edge from every ri to ri+1 , every node 
     * besides r1 has only one incoming edge, and every node besides rn
     * has only one outgoing edge.
     *
     * Action: Remove r1, …, rn, add a new node r = r1 … rn, redirect all 
     * incoming edges of r1 and all outgoing edges of rn to r. (In particular:
     * if G has an edge (rn, r1) then (r, r) is added.)
     *
     * @param slSingleOccurenceAutomaton $automaton 
     * @return void
     */
    protected function concatenation( slSingleOccurenceAutomaton $automaton )
    {
        $nodeCount = count( $this->nodes );
        $nodeNames = array_keys( $this->nodes );
        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            // Precondition
            if ( count( $outgoing = $automaton->getOutgoing( $nodeNames[$i] ) ) !== 1 )
            {
                continue;
            }

            // Collect nodes
            $nodes = array( $nodeNames[$i] );
            while ( ( count( $outgoing ) === 1 ) &&
                    ( count( $incoming = $automaton->getIncoming( $outgoing[0] ) ) === 1 ) )
            {
                if ( in_array( $outgoing[0], $nodes, true ) ||
                     count( array_intersect( $nodes, $automaton->getOutgoing( $outgoing[0] ) ) ) )
                {
                    // Do not find (indirect) self-loops here
                    continue 2;
                }

                $nodes[]  = $outgoing[0];
                $outgoing = $automaton->getOutgoing( $outgoing[0] );
            }

            if ( count( $nodes ) <= 1 )
            {
                continue;
            }

            // Action
            // Create a sequence out of the found sequence
            $incoming = $automaton->getIncoming( $nodeNames[$i] );

            // Merge nodes, if they share the same precedessors and 
            // successors.
            $sequence  = array();
            foreach ( $nodes as $node )
            {
                $sequence[] = $this->nodes[$node];
                unset( $this->nodes[$node] );
                $automaton->removeNode( $node );
            }
            
            $this->nodes[$newNode = $this->getUniqueNodeName()] = new slRegularExpressionSequence( $sequence );
            $automaton->addNode( $newNode );

            foreach ( $incoming as $node )
            {
                $automaton->addEdge( $node, $newNode );
            }

            foreach ( $outgoing as $node )
            {
                $automaton->addEdge( $newNode, $node );
            }

            return true;
        }

        return false;
    }

    /**
     * Self loop rule
     *
     * Precondition: (r, r) ∈ E.
     *
     * Action: Delete (r, r), relabel r by r+.
     *
     * @param slSingleOccurenceAutomaton $automaton 
     * @return void
     */
    protected function selfLoop( slSingleOccurenceAutomaton $automaton )
    {
        $nodeCount = count( $this->nodes );
        $nodeNames = array_keys( $this->nodes );
        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            // Precondition
            if ( in_array( $nodeNames[$i], $automaton->getOutgoing( $nodeNames[$i] ), true ) )
            {
                // Action
                $this->nodes[$nodeNames[$i]] = new slRegularExpressionRepeated( $this->nodes[$nodeNames[$i]] );
                $automaton->removeEdge( $nodeNames[$i], $nodeNames[$i] );
                return true;
            }
        }

        return false;
    }

    /**
     * Check if subset subsumes set
     *
     * Check if the subset is entirely contained in the set, and return true in 
     * this case - return false otherwise.
     * 
     * @param array $set 
     * @param array $subset 
     * @return bool
     */
    protected function superset( array $set, array $subset )
    {
        sort( $set );
        sort( $subset );
        return array_values( array_intersect( $set, $subset ) ) === array_values( $subset );
    }

    /**
     * Optional rule
     *
     * Precondition: Every r′ ∈ Pred(r), Succ(r) ⊆ Succ(r′). (Thus:
     * every node that can be reached through r from a predecessor, can
     * also be reached directly from that predecessor.)
     *
     * Action: Relabel r by r?, remove all edges (r′ , r′′) such that 
     * r′ ∈ Pred(r) and r′′ ∈ Succ(r) \ {r}.
     *
     * @param slSingleOccurenceAutomaton $automaton 
     * @return void
     */
    protected function optional( slSingleOccurenceAutomaton $automaton )
    {
        $nodeCount = count( $this->nodes );
        $nodeNames = array_keys( $this->nodes );
        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            // Precondition
            $outgoing = $automaton->getOutgoing( $nodeNames[$i] );
            $incoming = $automaton->getIncoming( $nodeNames[$i] );
            if ( !count( $incoming ) || !count( $outgoing ) )
            {
                continue;
            }

            foreach ( $incoming as $precedessor )
            {
                if ( !$this->superset( $automaton->getOutgoing( $precedessor ), $outgoing ) )
                {
                    continue 2;
                }
            }

            // Action
            $this->nodes[$nodeNames[$i]] = new slRegularExpressionOptional( $this->nodes[$nodeNames[$i]] );
            foreach ( $incoming as $src )
            {
                if ( $src === $nodeNames[$i] )
                {
                    continue;
                }

                foreach ( $outgoing as $dst )
                {
                    if ( $dst === $nodeNames[$i] )
                    {
                        continue;
                    }

                    $automaton->removeEdge( $src, $dst );
                }
            }

            return true;
        }

        return false;
    }
}

