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
class slSchemaXmlSchemaVisitor extends slSchemaVisitor
{
    /**
     * Types, defined in processed schema, to return references to the types 
     * for sub-visitors, like the regular expression visitor.
     * 
     * @var array
     */
    protected $types = array();

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
        $root->setAttribute( 'targetNamespace', 'http://example.com/generated' );
        $root->setAttribute( 'elementFormDefault', 'qualified' );
        $doc->appendChild( $root );

        $rootElements = $schema->getRootElements();

        $visitedTypes = array();
        foreach ( ( $this->types = $schema->getTypes() ) as $type )
        {
            if ( $elementName = array_search( $type->type, $rootElements ) )
            {
                $element = $doc->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'element' );
                $element->setAttribute( 'name', $elementName );
                $element->setAttribute( 'type', $type->type );
                $root->appendChild( $element );
            }

            if ( !isset( $visitedTypes[$type->type->name] ) )
            {
                $this->visitType( $root, $type );
                $visitedTypes[$type->type->name] = true;
            }
        }

        return $doc->saveXml();
    }

    /**
     * Visit single type / element
     *
     * Vist a single element and create proper XML Schema markup depending on 
     * the elements contents.
     * 
     * @param DOMElement $root 
     * @param slSchemaElement $element 
     * @return void
     */
    protected function visitType( DOMElement $root, slSchemaElement $element )
    {
        $regExpVisitor = new slRegularExpressionXmlSchemaVisitor( $this, $root->ownerDocument );

        switch ( true )
        {
            // For all non-empty regular expressions in the element, we can 
            // create a complex type definition. This can optionally be mixed
            // (for non-empty simple type definitions) and can always contain 
            // attribute definitions, too.
            case ( !$element->type->regularExpression instanceof slRegularExpressionEmpty ):
                
                $complexType = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'complexType' );
                $complexType->setAttribute( 'name', $element->type->name );
                $complexType->appendChild( $regExpVisitor->visit( $element->type->regularExpression ) );

                if ( $element->type->simpleTypeInferencer->inferenceType() !== 'empty' )
                {
                    $complexType->setAttribute( 'mixed', 'true' );
                }

                $this->visitAttributeList( $complexType, $element );

                $root->appendChild( $complexType );
                return;

            // For all following cases the regular expression can be considered 
            // empty. If the simple type definition also contains attributes we 
            // need to create a simple content node, this happens here:
            case ( count( $element->type->attributes ) ) &&
                 ( $element->type->simpleTypeInferencer->inferenceType( ) !== 'empty' ):

                $complexType = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'complexType' );
                $complexType->setAttribute( 'name', $element->type->name );
                $root->appendChild( $complexType );

                // Attach a simple content node to the complex type
                $simpleContent = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'simpleContent' );
                $complexType->appendChild( $simpleContent );

                // Extend the defined simple type by the list of attributes
                $extension = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'extension' );
                $extension->setAttribute( 'base', $this->getXmlSchemaSimpleType( $element->type->simpleTypeInferencer ) );
                $simpleContent->appendChild( $extension );

                $this->visitAttributeList( $extension, $element );
                return;

            // If we have an empty regular expression, and an empty simple type 
            // definition, we need to create a complext empty type, with 
            // attributes.
            case ( count( $element->type->attributes ) ):
                
                $complexType = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'complexType' );
                $complexType->setAttribute( 'name', $element->type->name );
                $this->visitAttributeList( $complexType, $element );
                $root->appendChild( $complexType );
                return;

            // The other two cases, where no attributes and no regular 
            // expression are provided for the element are handled by the 
            // regular expression visitor.
            default:
                return;
        }
    }

    /**
     * Visit the attribute list of an element into the given root element.
     * 
     * @param DOMElement $root 
     * @param slSchemaElement $element 
     * @return void
     */
    protected function visitAttributeList( DOMElement $root, slSchemaElement $element )
    {
        if ( !count( $element->type->attributes ) )
        {
            return;
        }

        foreach ( $element->type->attributes as $attribute )
        {
            $attributeNode = $root->ownerDocument->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'attribute' );
            $attributeNode->setAttribute( 'name', $attribute->name );
            $attributeNode->setAttribute( 'type', $this->getXmlSchemaSimpleType( $attribute->simpleTypeInferencer ) );
            
            if ( !$attribute->optional )
            {
                $attributeNode->setAttribute( 'use', 'required' );
            }

            $root->appendChild( $attributeNode );
        }
    }

    /**
     * Get XML Schema simple type
     *
     * Return an XML Schema simple type specification from the inferenced type 
     * provided by the given simple type inferencer.
     * 
     * @param slSimpleTypeInferencer $typeInferencer 
     * @return string
     */
    public function getXmlSchemaSimpleType( slSimpleTypeInferencer $typeInferencer )
    {
        switch ( $type = $typeInferencer->inferenceType() )
        {
            case 'empty':
            case 'PCDATA':
                return 'string';

            default:
                // @todo: Throw proper exception
                throw new RuntimeException( "Unhandled inferenced simple type '$type'." );
        }
    }

    /**
     * Get element type definition for type identifier
     *
     * Returns the element type definition, specified in a slSchemaElement 
     * object for the given type identifier.
     * 
     * @param string $type 
     * @return slSchemaElement
     */
    public function getType( $type )
    {
        return $this->types[$type];
    }

    /**
     * Set types array
     *
     * Method only used for testing, to set the contained types in the schema.
     * 
     * @param array $types
     * @return void
     * @access private
     */
    public function setTypes( array $types )
    {
        $this->types = $types;
    }
}

