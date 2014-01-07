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
 * Extension of the CRX algorithm to also inference the XML Schema ALL regular 
 * expression syntax.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slEChareConverter extends slChareConverter
{
    protected static $i = 1;

    /**
     * Build regular expression
     *
     * Builds the regilar expression from the provided automaton, where each 
     * node associates with one of the equivalency classes, and a topologically 
     * sorted list of these nodes.
     *
     * The provided automaton must be an instance of the 
     * slCountingSingleOccurenceAutomaton, so t at it provides information on 
     * how often the token occur in the learned strings.
     * 
     * @param slCountingSingleOccurenceAutomaton $automaton 
     * @param array $classes 
     * @return slRegularExpression
     */
    protected function buildRegularExpression( slCountingSingleOccurenceAutomaton $automaton, array $classes )
    {
        // xsd:all may only occur outmost and makes only sense for equivalence 
        // classes with more then one elment.
        if ( count( $classes ) === 1 )
        {
            $class = reset( $classes );
            $term  = $eClasses = $this->equivalenceClasses[$class];
            $count = $automaton->getOccurenceSum( $eClasses );
            $nodes = $automaton->getNodes();

            $generalCount = $automaton->getGeneralOccurences( $eClasses );
            if ( ( count( $term ) > 1 ) &&
                 ( $generalCount['max'] === 1 ) &&
                 ( $count['max'] > 1 ) )
            {
                // Inference all
                $term = new slRegularExpressionAll( array_map(
                    function( $term ) use ( $nodes )
                    {
                        return new slRegularExpressionElement( $nodes[$term] );
                    },
                    $term 
                ) );
                $term->minOccurences = $generalCount['min'];

                return $term;
            }
        }

        return parent::buildRegularExpression( $automaton, $classes );
    }
}

