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
class slSchemaXmlSchemaVisitor extends slSchemaVisitor
{
    /**
     * Root elements
     * 
     * @var array
     */
    protected $roots;

    /**
     * Construct XSD visitor
     *
     * A XML Schema schema may consist of any number of root elements. The 
     * constructor parameter let you define which of the found elements should 
     * be made available as root elements in the generated schema.
     * 
     * @param array $root 
     * @return void
     */
    public function __construct( array $rootElements )
    {
        $this->roots = $rootElements;
    }

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
        $doc = new DOMDocument();
        $doc->formatOutput = true;

        $root = $doc->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'schema' );
        $root->setAttribute( 'targetNamespace', 'http://example.com/gegenrated' );
        $root->setAttribute( 'elementFormDefault', 'qualified' );
        $doc->appendChild( $root );

        $regExpVisitor = new slRegularExpressionXmlSchemaVisitor( $doc );
        foreach ( $schema->getTypes() as $type )
        {
            if ( in_array( $type, $this->roots ) )
            {
                $element = $doc->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'element' );
                $element->setAttribute( 'name', $type->type );
                $element->setAttribute( 'type', $type->type );
                $root->appendChild( $element );
            }

            $complexType = $doc->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'complexType' );
            $complexType->setAttribute( 'name', $type );
            $complexType->appendChild( $regExpVisitor->visit( $type->regularExpression ) );
            $root->appendChild( $complexType );
        }

        return $doc->saveXml();
    }
}

