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
 * Type merger.
 *
 * Abstract class, which offers the API for type mergers, which evaluate sets 
 * of elements as equivalent.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slConfigurableTypeMerger extends slTypeMerger
{
    /**
     * Comparator used to compare the pattern defined in a type.
     * 
     * @var slSchemaTypePatternComparator
     */
    protected $patternComparator;

    /**
     * Comparator used to compare the attributes defined in a type.
     * 
     * @var slSchemaTypeAttributeComparator
     */
    protected $attributeComparator;

    /**
     * Construct configurable type merger from comparators
     *
     * Construct the type merger from an instance of a pattern comparator and 
     * an attribute comparator.
     * 
     * @param slSchemaTypePatternComparator $patternComparator 
     * @param slSchemaTypeAttributeComparator $attributeComparator 
     * @return void
     */
    public function __construct( slSchemaTypePatternComparator $patternComparator, slSchemaTypeAttributeComparator $attributeComparator )
    {
        $this->patternComparator   = $patternComparator;
        $this->attributeComparator = $attributeComparator;
    }

    /**
     * Group equivalent elements
     *
     * Receives an array with elements => automaton pairs. Returns an identical 
     * array, or leaves the type association array, as it was.
     *
     * If the elements are transformed it is especially important, that the elements 
     * references, referenced in the automatons, itself, are also updated with 
     * the new type names.
     * 
     * @param array $elements
     * @return array
     */
    public function groupTypes( array $elements )
    {
        do {
            $changed   = false;
            $typeCount = count( $elements );
            $typeIndex = array_keys( $elements );

            for ( $i = 0; $i < $typeCount; ++$i )
            {
                for ( $j = $i + 1; $j < $typeCount; ++$j )
                {
                    $elementI = $elements[$typeIndex[$i]];
                    $elementJ = $elements[$typeIndex[$j]];

                    // Skip if the type already points to the same object
                    if ( $elementI->type === $elementJ->type )
                    {
                        continue;
                    }

                    if ( $this->patternComparator->compare( $elementI->type, $elementJ->type ) &&
                         $this->attributeComparator->compare( $elementI->type, $elementJ->type ) )
                    {
                        $this->mergeTypes( $elements, $typeIndex[$i], $typeIndex[$j] );
                        $changed = true;
                        break 2;
                    }
                }
            }

        } while ( $changed );

        return $elements;
    }
}

