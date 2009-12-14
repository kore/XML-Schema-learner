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
 * Class representing an element / type in an inferenced schema
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaElement
{
    /**
     * Type of the schema element.
     *
     * The type is a string sufficiently unique for the types occuring in the
     * schema.
     * 
     * @var string
     */
    protected $type;

    /**
     * Simple type inferencer
     *
     * Instance of a simple type inferencer, to which the text contents of the
     * element will be passed. The simple typoe inferencer might then be used
     * to inference a simple type for the current element.
     * 
     * @var slSimpleTypeInferencer
     */
    protected $simpleTypeInferencer = null;

    /**
     * Attribute type inferencer
     *
     * Instance of a simple type inferencer, to which the text contents of the
     * attributes will be passed. The simple typoe inferencer might then be 
     * used to inference a simple type for the current attribute.
     * 
     * @var slSimpleTypeInferencer
     */
    protected $attributeTypeInferencer = null;

    /**
     * Array with attribute oocuring in the elment
     * 
     * @var array(slSchemaAttribute)
     */
    protected $attributes = array();

    /**
     * The element occured without any child elements at elast once in the
     * scanned schemas
     * 
     * @var bool
     */
    protected $empty = false;

    /**
     * Child occurence automaton
     *
     * Automaton to caclculate regular expressions for child pattern regular
     * expressions from. User to learn the sequences of the provided child
     * pathes.
     * 
     * @var slCountingSingleOccurenceAutomaton
     */
    protected $automaton = null;

    /**
     * Regular expression for type
     *
     * Contains the regular expression for this type. Will not be calculated
     * internally, but is expected to be updated from outside, and be
     * inferenced from the aggregated automaton.
     * 
     * @var mixed
     */
    protected $regularExpression = null;

    /**
     * Construct a schema element from a string type representation
     * 
     * @param string $type 
     * @return void
     */
    public function __construct( $type )
    {
        $this->type      = $type;
        $this->automaton = new slCountingSingleOccurenceAutomaton();
    }

    /**
     * Learn attributes
     *
     * Learn the given attributes. This methods receives an array with the 
     * attribute values of one element instance, like:
     *
     * <code>
     *  array(
     *      'name_1' => 'value',
     *      …
     *  )
     * </code>
     *
     * From all instances this method should learn, if the attribute is 
     * optional, or mandatory. It should also dispatch the values to the simple 
     * type inferencer for the given attribute.
     * 
     * @param array $attributes 
     * @return void
     */
    public function learnAttributes( array $attributes )
    {
        // First set of attributes, just add all
        if ( !count( $this->attributes ) )
        {
            foreach ( $attributes as $name => $value )
            {
                $this->attributes[$name] = new slSchemaAttribute( $name, clone $this->attributeTypeInferencer );
                $this->attributes[$name]->simpleTypeInferencer->learnString( $value );
            }
            return;
        }

        // First check for reoccurences of already known attributes. If 
        // attribute does not reoocur store as optional.
        foreach ( $this->attributes as $name => $attribute )
        {
            if ( isset( $attributes[$name] ) )
            {
                $this->attributes[$name]->simpleTypeInferencer->learnString( $attributes[$name] );
            }
            else
            {
                $this->attributes[$name]->optional = true;
            }
            unset( $attributes[$name] );
        }

        // Add all new attributes as optional to the list
        foreach ( $attributes as $name => $value )
        {
            $this->attributes[$name] = new slSchemaAttribute( $name, clone $this->attributeTypeInferencer );
            $this->attributes[$name]->simpleTypeInferencer->learnString( $value );
            $this->attributes[$name]->optional = true;
        }
    }

    /**
     * Get property from object
     *
     * Provides limited read-access to some of the object properties.
     *
     * Throws a runtime exception, if a property does not allow write access,
     * or does not exist.
     * 
     * @param string $property 
     * @return mixed
     */
    public function __get( $property )
    {
        switch ( $property )
        {
            case 'type':
            case 'empty':
            case 'automaton':
            case 'attributes':
            case 'regularExpression':
            case 'simpleTypeInferencer':
            case 'attributeTypeInferencer':
                return $this->$property;

            default:
                throw new RuntimeException( "Property '$property' is not available." );
        }
    }

    /**
     * Set property value
     *
     * Provides limited write-acces to a subset of the properties of the
     * element object.
     *
     * Throws a runtime exception, if a property does not allow write access,
     * or does not exist.
     * 
     * @param string $property 
     * @param mixed $value 
     * @return void
     */
    public function __set( $property, $value )
    {
        switch ( $property )
        {
            case 'empty':
            case 'regularExpression':
            case 'simpleTypeInferencer':
            case 'attributeTypeInferencer':
                return $this->$property = $value;

            default:
                throw new RuntimeException( "Property '$property' is not available." );
        }
    }

    /**
     * Return type es string representation of the element
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}

