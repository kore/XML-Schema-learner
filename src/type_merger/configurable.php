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
     * Comparator used to compare the pattern defined in a type, for elements 
     * which have the same name.
     * 
     * @var slSchemaTypePatternComparator
     */
    protected $snPatternComparator;

    /**
     * Comparator used to compare the attributes defined in a type, for 
     * elements which have the same name.
     * 
     * @var slSchemaTypeAttributeComparator
     */
    protected $snAttributeComparator;

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
        $this->patternComparator     = $patternComparator;
        $this->attributeComparator   = $attributeComparator;
        $this->snPatternComparator   = null;
        $this->snAttributeComparator = $attributeComparator;
    }

    /**
     * Set atribute comparator for elements with the same name
     * 
     * @param slSchemaTypeAttributeComparator $attributeComparator 
     * @return void
     */
    public function setSameNameAttributeComparator( slSchemaTypeAttributeComparator $attributeComparator )
    {
        $this->snAttributeComparator = $attributeComparator;
    }

    /**
     * Set atribute comparator for elements with the same name
     * 
     * @param slSchemaTypePatternComparator $patternComparator 
     * @return void
     */
    public function setSameNamePatternComparator( slSchemaTypePatternComparator $patternComparator )
    {
        $this->snPatternComparator = $patternComparator;
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
        if ( $this->snPatternComparator !== null )
        {
            // Group elements with the same name first, if a special type 
            // comperator has been provided for them.
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

                        if ( ( $elementI->name === $elementJ->name ) &&
                             ( $this->snPatternComparator->compare( $elementI->type, $elementJ->type ) &&
                               $this->snAttributeComparator->compare( $elementI->type, $elementJ->type ) ) )
                        {
                            $this->mergeTypes( $elements, $typeIndex[$i], $typeIndex[$j] );
                            $elements = $this->applyTypeMapping( $elements );
                            $changed = true;
                            break 2;
                        }
                    }
                }

            } while ( $changed );
        }

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
                        $elements = $this->applyTypeMapping( $elements );
                        $changed = true;
                        break 2;
                    }
                }
            }

        } while ( $changed );

        return $elements;
    }

    /**
     * Apply type mapping to type automatons
     *
     * When ytpes are merged, all references to those types must be updated in 
     * the type automatons. This method takes care of that.
     *
     * It returns an array with updated elements, and also removes types from 
     * the array, which are no longer used.
     * 
     * @param array $elements 
     * @return array
     */
    protected function applyTypeMapping( array $elements )
    {
        // Map all type references in automatons
        foreach ( $elements as $element )
        {
            foreach ( $element->type->automaton->getNodes() as $node )
            {
                if ( ( $node instanceof slSchemaAutomatonNode ) &&
                     isset( $this->typeMapping[$node->type] ) )
                {
                    $newNode = clone $node;
                    $newNode->type = $this->typeMapping[$node->type];
                    $element->type->automaton->renameNode( $node, $newNode );
                }
            }
        }

        // Remove the old type, since it shouldn't be referenced anywhere 
        // anymore.
        foreach ( $this->typeMapping as $removedType => $newType )
        {
            if ( isset( $elements[$removedType] ) )
            {
                unset( $elements[$removedType] );
            }
        }

        return $elements;
    }
}

