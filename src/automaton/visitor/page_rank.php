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
 * Automaton visitor base class.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slAutomatonPageRankVisitor extends slAutomatonVisitor
{
    /**
     * Number of iterations used for the PageRank algorithm.
     * 
     * @var int
     */
    protected $iterations = 25;

    /**
     * Damping factor used in the PageRank calculation.
     * 
     * @var float
     */
    protected $dampingFactor = .85;

    /**
     * Construct PageRank visitor
     *
     * Construct PageRank visitor from number of iterations to execute and a 
     * damping factor.
     * 
     * @param int $iterations 
     * @param float $dampingFactor 
     * @return void
     */
    public function __construct( $iterations = 25, $dampingFactor = .85 )
    {
        $this->iterations    = $iterations;
        $this->dampingFactor = $dampingFactor;
    }

    /**
     * Main visit function
     *
     * Visit function to process an automaton (directed, cyclic, discontinuous 
     * graph). No more detailed methods are defined, since the process of 
     * visiting is highly dependent on the concrete visitor implementation.
     *
     * Optionally labels may be passed for each node in the graph, which might 
     * be used during the rendering process.
     *
     * Returns an array with values for each node, representing their "imporance" 
     * in the graph based on Googles PageRank algorithm.
     * 
     * @param slAutomaton $automaton
     * @return array
     */
    public function visit( slAutomaton $automaton, array $labels = array() )
    {
        $nodes = $automaton->getNodes();
        $ranks = array();
        foreach ( $nodes as $node )
        {
            $ranks[$node] = 1;
        }

        for ( $i = 0; $i < $this->iterations; ++$i )
        {
            foreach ( $nodes as $node )
            {
                $rank = 0;
                foreach ( $automaton->getIncoming( $node ) as $src )
                {
                    $pr    = $ranks[$src];
                    $c     = count( $automaton->getOutgoing( $src ) );
                    $rank += ($pr / $c);
                }
                $ranks[$node] = ( ( 1 - $this->dampingFactor ) ) + $this->dampingFactor * $rank;
            }
        }

        return $ranks;
    }
}

