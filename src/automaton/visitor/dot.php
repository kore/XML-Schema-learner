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
class slAutomatonDotVisitor extends slAutomatonVisitor
{
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
     * Returns a string, containing a DOT specification of the graph, to be 
     * rendered with graphviz.
     * 
     * @param slAutomaton $automaton
     * @param array $labels
     * @return string
     */
    public function visit( slAutomaton $automaton, array $labels = array() )
    {
        $content = <<<EOSTYLE
digraph G 
{
    node [
        fontname  = Arial,
        fontcolor = "#2e3436",
        fontsize  = 10,

        style     = filled,
        color     = "#2e3436",
        fillcolor = "#babdb6",
        shape     = ellipse
    ];

    splines = true;
    overlap = false;

EOSTYLE;

        foreach ( $automaton->getNodes() as $node )
        {
            $content .= sprintf( "    \"%s\" [label = \"%s\"]\n",
                $node,
                isset( $labels[(string) $node] ) ? $labels[(string) $node] : $node
            );
        }
        $content .= "\n";

        foreach ( $automaton->getNodes() as $node )
        {
            foreach ( $automaton->getOutgoing( $node ) as $dst )
            {
                if ( $automaton instanceof slTypeAutomaton )
                {
                    $content .= sprintf( "    \"%s\" -> \"%s\" [label = \"%s\"]\n",
                        $node,
                        $dst,
                        $automaton->getEdgeLabel( $node, $dst )
                    );
                }
                else
                {
                    $content .= sprintf( "    \"%s\" -> \"%s\"\n",
                        $node,
                        $dst
                    );
                }
            }
        }

        return $content . "}\n";
    }
}

