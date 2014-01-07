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
class slTypeAutomaton extends slAutomaton
{
    /**
     * Edges in a directed graph in a reverse notation, for faster access of "parent" 
     * nodes.
     * 
     * @var array
     */
    protected $reverseEdges = array();

    /**
     * Add a directed edge to the graph
     * 
     * @param string $src 
     * @param string $dst 
     * @return void
     */
    public function addEdge( $src, $dst, $label = true )
    {
        parent::addEdge( $src, $dst );
        $this->reverseEdges[(string) $dst][(string) $src] = $label;
    }

    /**
     * Get edge label
     * 
     * @param string $src 
     * @param string $dst 
     * @return string
     */
    public function getEdgeLabel( $src, $dst )
    {
        return $this->reverseEdges[(string) $dst][(string) $src];
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
        if ( parent::removeEdge( $src, $dst ) )
        {
            unset( $this->reverseEdges[(string) $dst][(string) $src] );
            return true;
        }

        return false;
    }

    /**
     * Get ancestor patterns
     *
     * Return ancestor patterns for each type in the automaton.
     *
     * Returns an array with all types (nodes) as indexes, associated with an 
     * array of patterns, which identify the given type, which each consists of 
     * an array of types identifiers.
     * 
     * @return array
     */
    public function getAncestorPatterns()
    {
        $patterns = array();
        $paths   = array();
        foreach ( $this->reverseEdges as $dst => $childs )
        {
            foreach ( $childs as $src => $label )
            {
                $paths[$label][] = array(
                    $dst => true,
                    $src => $label,
                );
            }
        }

        foreach ( $paths as $label => $labelPaths )
        {
            if ( count( array_unique( array_map(
                    function( $path )
                    {
                        reset( $path );
                        return key( $path );
                    },
                    $labelPaths
                ) ) ) === 1 )
            {
                $path = reset( $labelPaths );
                $patterns[key( $path )][] = array_values( $path );
                continue;
            }

            $uniquePaths = $this->getUniquePaths( $labelPaths );
            foreach ( $uniquePaths as $type => $path )
            {
                $patterns[$type] = array_values( $path );
            }
        }

        // Normalize paths
        foreach ( $patterns as $type => $paths )
        {
            foreach ( $paths as $nr => $path )
            {
                $patterns[$type][$nr] = array_reverse( array_filter(
                    array_values( $path ),
                    function ( $label )
                    {
                        return $label !== true;
                    }
                ) );
            }
        }
        return $patterns;
    }

    /**
     * Get unique paths for a set of nodes.
     *
     * Extend the given paths to all their respective aprent nodes, until a 
     * path is "unique". In this case the path is stored as one possible 
     * path. This is iterated until a unique path is found for each path.
     *
     * Returns an arary with the unique paths.
     * 
     * @param array $paths 
     * @return array
     */
    protected function getUniquePaths( array $paths )
    {
        $uniquePaths = array();

        do {
            $newPaths = array();
            $pathCount = array();
            foreach ( $paths as $type => $path )
            {
                end( $path );
                $currentType = key( $path );

                if ( $currentType === '_start' )
                {
                    // Path reached the root node. Flag as such an store as 
                    // unique path.
                    $path[] = '^';
                    reset( $path );
                    $uniquePaths[key( $path )][] = $path;
                    continue;
                }

                foreach ( $this->reverseEdges[$currentType] as $src => $label )
                {
                    if ( isset( $path[$src] ) )
                    {
                        // Path recursively referring to contained elements. 
                        // Ignore.
                        continue;
                    }

                    // Extend path
                    end( $path );
                    $newPaths[] = $newPath = array_merge(
                        $path,
                        array( $src => $label )
                    );

                    $serialized = $this->serializePath( $newPath );
                    $pathCount[$serialized] = isset( $pathCount[$serialized] ) ? $pathCount[$serialized] + 1 : 1;
                }
            }

            // Remove already unique paths
            foreach ( $newPaths as $nr => $path )
            {
                if ( $pathCount[$this->serializePath( $path )] <= 1 )
                {
                    // Remove all paths from the list, which already are 
                    // unique.
                    reset( $path );
                    $uniquePaths[key( $path )][] = $path;
                    unset( $newPaths[$nr] );
                }
            }

            $paths = $newPaths;
        } while ( count( $paths ) );

        return $uniquePaths;
    }

    /**
     * Simple path serialization function
     *
     * Returns a deterministic string identifier for a path.
     * 
     * @param array $path 
     * @return string
     */
    protected function serializePath( array $path )
    {
        return implode( '#', $path );
    }
}

