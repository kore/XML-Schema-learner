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
     * @param string $src 
     * @param string $dst 
     * @return void
     */
    public function addEdge( $src, $dst )
    {
        if ( !isset( $this->nodes[(string) $src] ) )
        {
            $this->nodes[(string) $src] = $src;
        }

        if ( !isset( $this->nodes[(string) $dst] ) )
        {
            $this->nodes[(string) $dst] = $dst;
        }

        // Count number of edges
        if ( !isset( $this->edges[(string) $src] ) ||
             !isset( $this->edges[(string) $src][(string) $dst] ) )
        {
            $this->edges[(string) $src][(string) $dst] = 1;
        }
        else
        {
            $this->edges[(string) $src][(string) $dst]++;
        }
    }

    /**
     * Get edge weight
     *
     * Return the weight of the requested edge. Will return a non-normalized integer number. 
     * If the edge does not exist, the method will return null.
     * 
     * @param mixed $src 
     * @param mixed $dst 
     * @return integer
     */
    public function getEdgeWeight( $src, $dst )
    {
        if ( !isset( $this->edges[(string) $src] ) ||
             !isset( $this->edges[(string) $src][(string) $dst] ) )
        {
            return null;
        }
        else
        {
            return $this->edges[(string) $src][(string) $dst];
        }
    }
}

