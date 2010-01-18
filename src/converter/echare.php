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
        $terms = array();
        foreach ( $classes as $class )
        {
            $term  = $nodes = $this->equivalenceClasses[$class];
            $count = $automaton->getOccurenceSum( $nodes );

            // Handle singletons first
            if ( count( $term ) === 1 )
            {
                $terms[] = $this->wrapCountingPattern(
                    $count,
                    new slRegularExpressionElement( reset( $term ) )
                );
                continue;
            }

            // Handle node equivalence classes with multiple elements
            $generalCount = $automaton->getGeneralOccurences( $nodes );
            if ( ( $generalCount['max'] === 1 ) &&
                 ( $count['max'] > 1 ) )
            {
                // Inference all
                $term = new slRegularExpressionAll( array_map( function( $term )
                    {
                        return new slRegularExpressionElement( $term );
                    },
                    $term 
                ) );
                $term->minOccurences = $generalCount['min'];
                $count = array( 'min' => 1, 'max' => 1 );
            }
            else
            {
                $term = new slRegularExpressionChoice( array_map( function( $term )
                    {
                        return new slRegularExpressionElement( $term );
                    },
                    $term 
                ) );
            }

            $terms[] = $this->wrapCountingPattern( $count, $term );
        }

        return new slRegularExpressionSequence( $terms );
    }
}

