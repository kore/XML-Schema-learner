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
 * XML-Schema-learner is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
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
     * Flag indicating wheather the types already have been merged
     * 
     * @var bool
     */
    protected $typesMerged = false;

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
     * @param array $path
     * @return void
     */
    protected function inferenceType( array $path )
    {
        return $this->typeInferencer->inferenceType( $path );
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
     * Get used type merger
     * 
     * @return slTypeMerger
     */
    public function getTypeMerger()
    {
        return $this->typeMerger;
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
        $reader = new XmlReader();
        $reader->open( $file );
        
        $this->traverse( $reader );

        $reader->close();
        unset( $reader );
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
    protected function traverse( XMLReader $reader, $path = array() )
    {
        $contents   = array();
        $whitespace = array();
        $children   = array();
        $attributes = array();

        // Learn attributes for this element
        while ( $reader->moveToNextAttribute() )
        {
            $attributes[$reader->name] = $reader->value;
        }
        $reader->moveToElement();

        // If this is an empty element, do not traverse, but return 
        // immediately.
        if ( ( $reader->nodeType === XMLReader::ELEMENT ) &&
             ( $reader->isEmptyElement ) )
        {
            $element = $this->getType( $path );

            $this->learnAutomaton( $element, $children );
            $this->learnSimpleType( $element, $contents );
            $this->learnAttributes( $element, $attributes );
            return;
        }

        // Traverse child elements.
        while ( $reader->read() )
        {
            switch ( $reader->nodeType )
            {
                case XMLReader::ELEMENT:
                    // Opening tag
                    $child = array(
                        'namespace' => $reader->namespaceURI,
                        'name'      => $reader->localName,
                        'parents'   => $path,
                    );
                    $children[] = $child;
                    $childPath  = array_merge( $path, array( $child ) );

                    // If we are in the document root, add the child as root 
                    // element.
                    if ( count( $path ) === 0 )
                    {
                        $this->rootElements[$child['name']] = $this->inferenceType( $childPath );
                    }

                    $this->traverse( $reader, $childPath );
                    break;

                case XMLReader::END_ELEMENT:
                    // Closing tag
                    $element = $this->getType( $path );

                    if (!count($children)) {
                        // Significant whitespace seems only significant if
                        // there are children, but there is still some
                        // whitespace.
                        $contents = array_merge($contents, $whitespace);
                    }

                    $this->learnAutomaton( $element, $children );
                    $this->learnSimpleType( $element, $contents );
                    $this->learnAttributes( $element, $attributes );
                    return;

                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    // Text content
                    $contents[] = $reader->value;
                    break;

                case XMLReader::SIGNIFICANT_WHITESPACE:
                    $whitespace[] = $reader->value;
                    break;
            }
        }
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
        if ( $this->typesMerged )
        {
            return $this->elements;
        }

        $this->elements = $this->typeMerger->groupTypes( $this->elements );

        $optimizer = new slRegularExpressionOptimizer();

        // Ensure the regular expressions in all elements are up to date
        foreach ( $this->elements as $type => $element )
        {
            $regularExpression = $this->convertRegularExpression( $element->type->automaton );
            $regularExpression = $this->filterStartEndMarkers( $regularExpression );

            // If the element has been empty at least once, make the whole 
            // subpattern optional
            if ( $element->type->empty )
            {
                $regularExpression = new slRegularExpressionOptional( $regularExpression );
            }

            // Second optimizing step
            $optimizer->optimize( $regularExpression );

            // Apply type mapping from type merger recursively to regular 
            // expression.
            $element->type->regularExpression = $regularExpression;
        };

        $this->typesMerged = true;
        return $this->elements;
    }

    /**
     * Recursively filters out start and end markers
     *
     * Recursively filter out start and end markers from the regular expression 
     * structure, since they do not have any real meaning, but were required to 
     * create correct automatons.
     * 
     * @param slRegularExpression $regularExpression 
     * @return slRegularExpression
     */
    protected function filterStartEndMarkers( slRegularExpression $regularExpression )
    {
        if ( $regularExpression instanceof slRegularExpressionMultiple )
        {
            foreach ( $regularExpression->getChildren() as $nr => $child )
            {
                if ( ( $child instanceof slRegularExpressionElement ) &&
                     ( ( $child->getContent() === 0 ) ||
                       ( $child->getContent() === 1 ) ) )
                {
                    $regularExpression[$nr] = new slRegularExpressionEmpty();
                }
                else
                {
                    $this->filterStartEndMarkers( $child );
                }
            }
        }

        if ( $regularExpression instanceof slRegularExpressionSingular )
        {
            $child = $regularExpression->getChild();
            if ( ( $child instanceof slRegularExpressionElement ) &&
                 ( ( $child->getContent() === 0 ) ||
                   ( $child->getContent() === 1 ) ) )
            {
                $regularExpression->setChild( new slRegularExpressionEmpty() );
            }
            else
            {
                $this->filterStartEndMarkers( $child );
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

        $elements = array( 0 );
        foreach ( $children as $child )
        {
            $elements[] = new slSchemaAutomatonNode(
                $child['name'],
                $this->inferenceType( array_merge( $child['parents'], array( $child ) ) )
            );
        }
        array_push( $elements, 1 );

        $element->type->automaton->learn( $elements );
    }

    /**
     * Lear simple type for element
     * 
     * @param slSchemaElement $element
     * @param array $contents 
     * @return void
     */
    protected function learnSimpleType( slSchemaElement $element, array $contents )
    {
        foreach ( $contents as $string )
        {
            $element->type->simpleTypeInferencer->learnString( $string );
        }
    }

    /**
     * Lear attributes for element
     * 
     * @param slSchemaElement $element
     * @param array $attributes 
     * @return void
     */
    protected function learnAttributes( slSchemaElement $element, array $attributes )
    {
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
     * @param array $path
     * @return slSchemaElement
     */
    protected function getType( array $path )
    {
        $elementTypeName = $this->inferenceType( $path );

        if ( isset( $this->elements[$elementTypeName] ) )
        {
            return $this->elements[$elementTypeName];
        }

        $current = end( $path );
        $this->elements[$elementTypeName] = $element = new slSchemaElement(
            $current['name'],
            new slSchemaType( $elementTypeName )
        );
        $element->type->simpleTypeInferencer    = $this->getSimpleInferencer();
        $element->type->attributeTypeInferencer = $this->getSimpleInferencer();
        return $element;
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
 
