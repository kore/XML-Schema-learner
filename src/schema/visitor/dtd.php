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
 * Base class for visiting schemas
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaDtdVisitor extends slSchemaVisitor
{
    /**
     * Visit a schema
     *
     * The visitor is not structured, since the types might be required to be 
     * iterated tree-based for more complex schema definitions (like XML Schema 
     * schemas).
     *
     * The return value depends on the concrete visitor implementation.
     * 
     * @param slSchema $schema 
     * @return string
     */
    public function visit( slSchema $schema )
    {
        $rootElements = $schema->getRootElements();
        
        if ( count( $rootElements ) > 1 )
        {
            // @todo: Use a proper exception here
            throw new RuntimeException( 'Invalid DTD schema: Too many root elements.' );
        }
        $root = reset( $rootElements );
        $dtd  = '';

        // Visit all elements / types
        foreach ( $schema->getTypes() as $element )
        {
            $dtd .= $this->visitElement( $element );
        }

        $dtd .= "\n";

        // Visit all attributes below the element definitions
        $regExpVisitor = new slRegularExpressionDtdVisitor();
        foreach ( $schema->getTypes() as $element )
        {
            foreach ( $element->type->attributes as $attribute )
            {
                $dtd .= $this->visitAttribute( $element, $attribute );
            }
        }

        return trim( $dtd ) . "\n";
    }

    /**
     * Visit element
     *
     * Create the attribute DTD specification from the provided 
     * slSchemaAttribute object.
     * 
     * @param slSchemaElement $element 
     * @param slSchemaAttribute $attribute 
     * @return string
     */
    protected function visitAttribute( slSchemaElement $element, slSchemaAttribute $attribute )
    {
        return sprintf( "<!ATTLIST %s %s CDATA %s>\n",
            $element->type,
            $attribute->name,
            $attribute->optional ? '#IMPLIED' : '#REQUIRED'
        );
    }

    /**
     * Visit element
     *
     * Create the element DTD specification from the provided slSchemaElement 
     * object.
     * 
     * @param slSchemaElement $element 
     * @return string
     */
    protected function visitElement( slSchemaElement $element )
    {
        $regExpVisitor = new slRegularExpressionDtdVisitor();
        switch ( true )
        {
            case ( $element->type->regularExpression instanceof slRegularExpressionEmpty ) &&
                 ( $element->type->simpleTypeInferencer->inferenceType() === 'empty' ):
                return sprintf( "<!ELEMENT %s EMPTY>\n",
                    $element->name
                );

            case ( $element->type->regularExpression instanceof slRegularExpressionEmpty ):
                return sprintf( "<!ELEMENT %s (#PCDATA)>\n",
                    $element->name
                );

            case ( $element->type->simpleTypeInferencer->inferenceType() === 'empty' ):
                return sprintf( "<!ELEMENT %s ( %s )>\n",
                    $element->name,
                    $regExpVisitor->visit( $element->type->regularExpression )
                );
            
            default:
                return sprintf( "<!ELEMENT %s ( #PCDATA | %s )*>\n",
                    $element->name,
                    implode( ' | ', array_map(
                        function( $type )
                        {
                            return $type->name;
                        },
                        $this->extractTypes( $element->type->regularExpression ) )
                    )
                );
        }
    }

    /**
     * Extracts all types mentioned in a regular exression
     *
     * Returns an unique array with all types mentioned in the regular 
     * expression, to create proper mixed types in the DTD schema output.
     * 
     * @param slRegularExpression $regularExpression 
     * @return array
     */
    protected function extractTypes( slRegularExpression $regularExpression )
    {
        if ( $regularExpression instanceof slRegularExpressionElement )
        {
            return array( $regularExpression->getContent() );
        }

        if ( !$regularExpression instanceof slRegularExpressionContainer )
        {
            return array();
        }

        $types = array();
        foreach ( $regularExpression->getChildren() as $child )
        {
            $types = array_merge( $types, $this->extractTypes( $child ) );
        }

        return array_unique( $types );
    }
}

