<?php
/**
 * Schema learning
 *
 * This file is part of SchemaLearner.
 *
 * SchemaLearner is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * SchemaLearner is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with SchemaLearner; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Basic converter for single occurence automatons to regular expressions
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
        $states = $automaton->getNodes();
        if ( ( $stateCount = count( $states ) ) <= 1 )
        {
            return new slRegularExpressionSequence( $states );
        }

        $this->nodes = array();
        foreach ( $states as $state )
        {
            $this->nodes[$state] = new slRegularExpressionSequence( array( $state ) );
        }

        do {
            $modification = false;
            
            $modification |= $this->disjunction( $automaton );
            $modification |= $this->concatenation( $automaton );
            $modification |= $this->selfLoop( $automaton );
            $modification |= $this->optional( $automaton );
        } while ( $modification );
    }

    /**
     * Disjuction rule
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
            for ( $j = $i + 1; $j < $nodeCount; ++$j )
            {
                if ( ( $automaton->getOutgoing( $nodeNames[$i] ) !== $automaton->getOutgoing( $nodeNames[$j] ) ) ||
                     ( $automaton->getIncoming( $nodeNames[$i] ) !== $automaton->getIncoming( $nodeNames[$j] ) ) )
                {
                    continue;
                }

                // Find further nodes sharing the same properties
                $nodes = array( $nodeNames[$i], $nodeNames[$j] );
                for ( $k = $j + 1; $k < $nodeCount; ++$k )
                {
                    if ( ( $automaton->getOutgoing( $nodeNames[$k] ) === $automaton->getOutgoing( $nodeNames[$j] ) ) &&
                         ( $automaton->getIncoming( $nodeNames[$k] ) === $automaton->getIncoming( $nodeNames[$j] ) ) )
                    {
                        $nodes[$nodeNames[$k]];
                    }
                }

                // Merge nodes, if they share the same precedessors and 
                // successors.
                var_dump( $nodes );
            }
        }

        return false;
    }
}

