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
     * Add a node, which is not connected to any other nodes
     * 
     * @param string $node 
     * @return void
     */
    public function addNode( $node )
    {
        $this->nodes[$node] = true;
        $this->edges[$node] = array();
    }

    /**
     * Remove the given node from the graph
     *
     * Removes the given node from the graph including all edges from and to 
     * the given node.
     *
     * Returns false, if the node did not exist, and true otherwise.
     * 
     * @param string $node 
     * @return bool
     */
    public function removeNode( $node )
    {
        if ( !isset( $this->nodes[$node] ) )
        {
            return false;
        }

        unset( $this->nodes[$node] );
        unset( $this->edges[$node] );
        foreach ( $this->edges as $source => $edge )
        {
            if ( isset( $edge[$node] ) )
            {
                unset( $this->edges[$source][$node] );
            }
        }

        return true;
    }

    /**
     * Remove the given edge from the graph
     *
     * Removes the given edge from the graph, but keep the associated nodes.
     *
     * Returns false, if one of the nodes did not exist, and true otherwise.
     * 
     * @param string $src 
     * @param string $dst 
     * @return bool
     */
    public function removeEdge( $src, $dst )
    {
        if ( !isset( $this->nodes[$src] ) ||
             !isset( $this->nodes[$dst] ) ||
             !isset( $this->edges[$src] ) ||
             !isset( $this->edges[$src][$dst] ) )
        {
            return false;
        }

        unset( $this->edges[$src][$dst] );
        return true;
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
        if ( !isset( $this->edges[$node] ) )
        {
            return array();
        }

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

    /**
     * Get transitive closure for node
     *
     * Returns an array of the nodes which build the transitive closure for the 
     * given node in the current automaton.
     * 
     * @param string $node 
     * @return array
     */
    public function transitiveClosure( $node )
    {
        $nodes = array( $node );
        do {
            $old = $nodes;
            $nodes = array_unique( 
                array_merge( $nodes,
                    array_reduce(
                        array_map(
                            array( $this, 'getOutgoing' ),
                            $nodes
                        ),
                        'array_merge',
                        array()
                    )
                )
            );
        } while ( $nodes != $old );

        return $nodes;
    }

    /**
     * Get leaves
     *
     * Return an array of "leaves" in the current graph. All nodes are 
     * considered leaves which do not have any outgoing edges.
     * 
     * @return array
     */
    public function getLeaves()
    {
        $leaves = array();
        foreach ( $this->nodes as $node => $true )
        {
            if ( !isset( $this->edges[$node] ) ||
                 ( count( $this->edges[$node] ) === 0 ) )
            {
                $leaves[] = $node;
            }
        }

        return $leaves;
    }

    /**
     * Get topologically sorted node list
     *
     * Returns an array of the nodes in the automaton, topologically sorted.
     * 
     * @return array
     */
    public function getTopologicallySortedNodeList()
    {
        $list      = array();
        $automaton = clone $this;
        $leaves    = $automaton->getLeaves();
        var_dumP( $leaves );

        while ( ( $leave = array_pop( $leaves ) ) !== null )
        {
            $list[] = $leave;
            $automaton->removeNode( $leave );
            $leaves = $automaton->getLeaves();
        }

        return array_reverse( $list );
    }
}

