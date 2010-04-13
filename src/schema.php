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
 * Class representing a schema.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slSchema
{
    /**
     * Array of elements
     *
     * Contains a list of all found elements with their context
     * information.
     *
     * The slSchemaElement contains information about the elements simple,
     * type, attriubutes and its regular expression.
     * 
     * @var array
     */
    protected $elements = array();

    /**
     * Types of found root elements
     * 
     * @var array
     */
    protected $rootElements = array();

    /**
     * Use type inferencer
     * 
     * @var slTypeInferencer
     */
    protected $typeInferencer;

    /**
     * Use type merger
     * 
     * @var slTypeMerger
     */
    protected $typeMerger;

    /**
     * Construct new schema class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->elements       = array();
        $this->rootElements   = array();
        $this->typeInferencer = new slNameBasedTypeInferencer();
        $this->typeMerger     = new slNoTypeMerger();
    }

    /**
     * Inference type from DOMElement
     * 
     * @param DOMElement $element 
     * @return void
     */
    protected function inferenceType( DOMElement $element )
    {
        return $this->typeInferencer->inferenceType( $element );
    }

    /**
     * Get schema dependent simple type inferencer
     * 
     * @return slSimpleTypeInferencer
     */
    abstract protected function getSimpleInferencer();

    /**
     * Set type inferencer
     * 
     * @param slTypeInferencer $typeInferencer 
     * @return void
     */
    public function setTypeInferencer( slTypeInferencer $typeInferencer )
    {
        $this->typeInferencer = $typeInferencer;
    }

    /**
     * Set type merger
     * 
     * @param slTypeMerger $typeMerger 
     * @return void
     */
    public function setTypeMerger( slTypeMerger $typeMerger )
    {
        $this->typeMerger = $typeMerger;
    }

    /**
     * Learn XML file
     *
     * Learn the automaton from an XML file
     * 
     * @param string $file 
     * @return void
     */
    public function learnFile( $file )
    {
        $doc = new DOMDocument();
        $doc->load( $file );
        $this->traverse( $doc );
    }

    /**
     * Get regular expressions for learned schema
     *
     * Get an array of type -> regular expression associations for the learned 
     * schema.
     * 
     * @return array(slSchemaElement)
     */
    public function getTypes()
    {
        $this->elements = $this->typeMerger->groupTypes( $this->elements );
        $typeMapping    = $this->typeMerger->getTypeMapping();

        $optimizer = new slRegularExpressionOptimizer();

        // Ensure the regular expressions in all elements are up to date
        foreach ( $this->elements as $type => $element )
        {
            $regularExpression = $this->convertRegularExpression( $element->type->automaton );

            // If the element has been empty at least once, make the whole 
            // subpattern optional
            if ( $element->type->empty )
            {
                $regularExpression = new slRegularExpressionOptional( $regularExpression );
            }

            // Optimize regular expression
            $optimizer->optimize( $regularExpression );
            $element->type->regularExpression = $this->applyTypeMapping( $regularExpression, $typeMapping );
        };

        return $this->elements;
    }

    /**
     * Apply type mapping
     *
     * Recursively replace types with replaced types in regular expression 
     * structures.
     * 
     * @param slRegularExpression $regularExpression 
     * @param array $typeMapping 
     * @return slRegularExpression
     */
    protected function applyTypeMapping( slRegularExpression $regularExpression, array $typeMapping )
    {
        if ( $regularExpression instanceof slRegularExpressionElement )
        {
            $content = $regularExpression->getContent();
            if ( isset( $typeMapping[$content->type] ) )
            {
                $content->type = $typeMapping[$content->type];
                $regularExpression->setContent( $content );
            }
        }

        if ( $regularExpression instanceof slRegularExpressionContainer )
        {
            foreach ( $regularExpression->getChildren() as $child )
            {
                $child = $this->applyTypeMapping( $child, $typeMapping );
            }
        }

        return $regularExpression;
    }

    /**
     * Return elements found as root elements
     *
     * Returns an array with the string representations of the elements, which
     * have been found as root elements in the provided schemas.
     * 
     * @return void
     */
    public function getRootElements()
    {
        return $this->rootElements;
    }

    /**
     * Lear Automaton for element
     * 
     * @param slSchemaElement $element
     * @param array $children 
     * @return void
     */
    protected function learnAutomaton( slSchemaElement $element, array $children )
    {
        if ( !count( $children ) )
        {
            $element->type->empty = true;
            return;
        }

        $elements = array();
        foreach ( $children as $child )
        {
            $elements[] = new slSchemaAutomatonNode( $child->tagName, $this->inferenceType( $child ) );
        }

        $element->type->automaton->learn( $elements );
    }

    /**
     * Lear simple type for element
     * 
     * @param slSchemaElement $element
     * @param array $children 
     * @return void
     */
    protected function learnSimpleType( slSchemaElement $element, array $children )
    {
        foreach ( $children as $textNode )
        {
            $element->type->simpleTypeInferencer->learnString( trim( $textNode->wholeText ) );
        }
    }

    /**
     * Lear attributes for element
     * 
     * @param slSchemaElement $element
     * @param array $children 
     * @return void
     */
    protected function learnAttributes( slSchemaElement $element, array $children )
    {
        $attributes = array();
        foreach ( $children as $attrNode )
        {
            $attributes[$attrNode->name] = $attrNode->value;
        }

        $element->type->learnAttributes( $attributes );
    }

    /**
     * Return element representation for the given type
     *
     * Return the element representation object for the provided type. If non
     * exists yet a new blank one will be created.
     *
     * The slSchemaElement contains information about the elements simple,
     * type, attriubutes and its regular expression.
     * 
     * @param string $type 
     * @return slSchemaElement
     */
    protected function getType( DOMElement $node )
    {
        $elementTypeName = $this->inferenceType( $node );

        if ( isset( $this->elements[$elementTypeName] ) )
        {
            return $this->elements[$elementTypeName];
        }

        $this->elements[$elementTypeName] = $element = new slSchemaElement(
            $node->tagName,
            new slSchemaType( $elementTypeName )
        );
        $element->type->simpleTypeInferencer    = $this->getSimpleInferencer();
        $element->type->attributeTypeInferencer = $this->getSimpleInferencer();
        return $element;
    }

    /**
     * Traverse XML tree
     *
     * Traverses the XML tree and calls the learnAutomaton() method for each 
     * element, with its child element nodes.
     * 
     * @param DOMNode $root 
     * @return void
     */
    protected function traverse( DOMNode $root )
    {
        if ( $root->parentNode instanceof DOMDocument )
        {
            $this->rootElements[$root->tagName] = $this->inferenceType( $root );
        }

        $elements = array();
        $contents = array();
        foreach ( $root->childNodes as $node )
        {
            switch ( $node->nodeType )
            {
                case XML_ELEMENT_NODE:
                    $elements[] = $node;
                    $this->traverse( $node );
                    break;

                case XML_TEXT_NODE:
                    $contents[] = $node;
                    break;
            }
        }

        // How inconsistent: Attributes are not available as child nodes. Build 
        // an extra loop just for them.
        $attributes = array();
        if ( $root->attributes )
        {
            foreach ( $root->attributes as $attribute )
            {
                $attributes[] = $attribute;
            }
        }

        if ( $root->nodeType === XML_ELEMENT_NODE )
        {
            $element = $this->getType( $root );

            $this->learnAutomaton( $element, $elements );
            $this->learnSimpleType( $element, $contents );
            $this->learnAttributes( $element, $attributes );
        }
    }

    /**
     * Convert automaton to regular expression
     * 
     * @param slAutomaton $automaton 
     * @return slRegularExpression
     */
    protected function convertRegularExpression( $automaton )
    {
        // Convert automatons
        $converter = new slSoreConverter();
        if ( ( $expression = $converter->convertAutomaton( $automaton ) ) !== false )
        {
            return $expression;
        }

        $converter = new slChareConverter();
        return $converter->convertAutomaton( $automaton );
    }
}
 
