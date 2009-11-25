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
 * Basic automaton class, which implements a simple graph with directed 
 * edges and the corresponding methods.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slAutomaton
{
    /**
     * Nodes in the directed graph
     * 
     * @var array
     */
    protected $nodes = array();

    /**
     * Edges in the directed graph
     * 
     * @var array
     */
    protected $edges = array();

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

        $this->edges[$start][$end] = true;
    }

    /**
     * Get all nodes
     * 
     * @return void
     */
    public function getNodes()
    {
        return array_keys( $this->nodes );
    }

    /**
     * Get nodes which can be reached by an edge from the given node
     * 
     * @param string $node 
     * @return array
     */
    public function getOutgoing( $node )
    {
        return array_keys( $this->edges[$node] );
    }

    /**
     * Get nodes from which an edge points to the given node
     * 
     * @param string $node 
     * @return array
     */
    public function getIncoming( $node )
    {
        $incoming = array();
        foreach ( $this->edges as $source => $edge )
        {
            if ( isset( $edge[$node] ) )
            {
                $incoming[$source] = true;
            }
        }

        return array_keys( $incoming );
    }
}

