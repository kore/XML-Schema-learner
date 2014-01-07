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

        $this->edges[(string) $src][(string) $dst] = true;
    }

    /**
     * Add a node, which is not connected to any other nodes
     * 
     * @param string $node 
     * @return void
     */
    public function addNode( $node )
    {
        $this->nodes[(string) $node] = $node;
        $this->edges[(string) $node] = array();
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
        if ( !isset( $this->nodes[(string) $node] ) )
        {
            return false;
        }

        unset( $this->nodes[(string) $node] );
        unset( $this->edges[(string) $node] );
        foreach ( $this->edges as $source => $edge )
        {
            if ( isset( $edge[(string) $node] ) )
            {
                unset( $this->edges[$source][(string) $node] );
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
        if ( !isset( $this->nodes[(string) $src] ) ||
             !isset( $this->nodes[(string) $dst] ) ||
             !isset( $this->edges[(string) $src] ) ||
             !isset( $this->edges[(string) $src][(string) $dst] ) )
        {
            return false;
        }

        unset( $this->edges[(string) $src][(string) $dst] );
        return true;
    }

    /**
     * Get all nodes
     * 
     * @return void
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * Get nodes which can be reached by an edge from the given node
     * 
     * @param string $node 
     * @return array
     */
    public function getOutgoing( $node )
    {
        if ( !isset( $this->edges[(string) $node] ) )
        {
            return array();
        }

        return array_keys( $this->edges[(string) $node] );
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
            if ( isset( $edge[(string) $node] ) )
            {
                $incoming[$source] = true;
            }
        }

        return array_keys( $incoming );
    }

    /**
     * Merge current automaton with given automaton
     * 
     * @param slAutomaton $automaton 
     * @return void
     */
    public function merge( slAutomaton $automaton )
    {
        foreach ( $automaton->getNodes() as $identifier => $node )
        {
            if ( !isset( $this->nodes[$identifier] ) )
            {
                $this->addNode( $node );
            }
        }

        foreach ( $automaton->getNodes() as $identifier => $node )
        {
            foreach ( $automaton->getOutgoing( $node ) as $dst )
            {
                $this->addEdge( $node, $dst );
            }
        }
    }

    /**
     * Rename a node
     *
     * Rename a node. If a node with the target name already exists the two 
     * nodes should be merged properly.
     * 
     * @param string $old 
     * @param string $new 
     * @return void
     */
    public function renameNode( $old, $new )
    {
        if ( !isset( $this->nodes[(string) $old] ) )
        {
            return;
        }

        if ( !isset( $this->nodes[(string) $new] ) )
        {
            $this->addNode( $new );
        }

        foreach ( $this->getOutgoing( (string) $old ) as $dst )
        {
            $this->addEdge( (string) $new, ( $dst === (string) $old ) ? (string) $new : $dst );
        }

        foreach ( $this->getIncoming( (string) $old ) as $src )
        {
            if ( $src !== (string) $old )
            {
                $this->addEdge( $src, (string) $new );
            }
        }

        $this->removeNode( (string) $old );
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
        $nodes = array( (string) $node );
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

        while ( ( $leave = array_pop( $leaves ) ) !== null )
        {
            $list[] = $leave;
            $automaton->removeNode( $leave );
            $leaves = $automaton->getLeaves();
        }

        return array_reverse( $list );
    }
}

