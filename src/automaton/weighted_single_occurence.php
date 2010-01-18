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
 * Single occurence automaton class, which implements and learns automatons 
 * from strings, where each string element only occurs once, and thus has a 
 * direct representation in the automaton.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slWeightedSingleOccurenceAutomaton extends slSingleOccurenceAutomaton
{
    /**
     * Add a directed edge to the graph
     * 
     * @param string $start 
     * @param string $end 
     * @return void
     */
    public function addEdge( $start, $end )
    {
        if ( !isset( $this->nodes[$start] ) )
        {
            $this->nodes[$start] = true;
        }

        if ( !isset( $this->nodes[$end] ) )
        {
            $this->nodes[$end] = true;
        }

        // Count number of edges
        if ( !isset( $this->edges[$start] ) ||
             !isset( $this->edges[$start][$end] ) )
        {
            $this->edges[$start][$end] = 1;
        }
        else
        {
            $this->edges[$start][$end]++;
        }
    }

    /**
     * Get edge weight
     *
     * Return the weight of the requested edge. Will return a non-normalized integer number. 
     * If the edge does not exist, the method will return null.
     * 
     * @param mixed $start 
     * @param mixed $end 
     * @return integer
     */
    public function getEdgeWeight( $start, $end )
    {
        if ( !isset( $this->edges[$start] ) ||
             !isset( $this->edges[$start][$end] ) )
        {
            return null;
        }
        else
        {
            return $this->edges[$start][$end];
        }
    }
}

