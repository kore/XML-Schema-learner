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
            throw new RuntimeException( 'Invaid DTD schema: Too many root elements.' );
        }
        $root = reset( $rootElements );

        $dtd = "<!DOCTYPE $root>\n\n";

        $regExpVisitor = new slRegularExpressionDtdVisitor();
        foreach ( $schema->getTypes() as $type )
        {
            switch ( true )
            {
                case ( $type->regularExpression instanceof slRegularExpressionEmpty ) &&
                     ( $type->simpleTypeInferencer->inferenceType() === 'empty' ):
                    $dtd .= sprintf( "<!ELEMENT %s EMPTY>\n", $type->type );
                    break;

                case ( $type->regularExpression instanceof slRegularExpressionEmpty ):
                    $dtd .= sprintf( "<!ELEMENT %s (#PCDATA)>\n", $type->type );
                    break;

                case ( $type->simpleTypeInferencer->inferenceType() === 'empty' ):
                    $dtd .= sprintf( "<!ELEMENT %s ( %s )>\n",
                        $type->type,
                        $regExpVisitor->visit( $type->regularExpression )
                    );
                    break;
                
                default:
                    $dtd .= sprintf( "<!ELEMENT %s ( #PCDATA | %s )*>\n",
                        $type->type,
                        $regExpVisitor->visit( $type->regularExpression )
                    );
                    break;
            }
        }

        $dtd .= "\n";

        $regExpVisitor = new slRegularExpressionDtdVisitor();
        foreach ( $schema->getTypes() as $type )
        {
            foreach ( $type->attributes as $attribute )
            {
                $dtd .= sprintf( "<!ATTLIST %s %s CDATA %s>\n",
                    $type->type,
                    $attribute->name,
                    $attribute->optional ? '#IMPLIED' : '#REQUIRED'
                );
            }
        }

        return $dtd;
    }
}

