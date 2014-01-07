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
 * Class representing an type in an inferenced schema
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaType
{
    /**
     * Name of the type
     * 
     * @var string
     */
    protected $name;

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
    protected $attributes = null;

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
    public function __construct( $name )
    {
        $this->name                    = $name;
        $this->automaton               = new slCountingSingleOccurenceAutomaton();
        $this->attributeTypeInferencer = new slPcdataSimpleTypeInferencer();
        $this->simpleTypeInferencer    = new slPcdataSimpleTypeInferencer();
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
        if ( $this->attributes === null )
        {
            $this->attributes = array();
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
     * Merge type with another type
     *
     * @todo: Ignore simple types, for now, and especially does not merge 
     * attribute types. Only builds a list with all attributes from all merged 
     * types.
     * 
     * @param slSchemaType $type 
     * @return void
     */
    public function merge( slSchemaType $type )
    {
        // Merge simple type
        $this->empty = $this->empty & $type->empty;

        // Merge attributes
        foreach ( $type->attributes as $name => $attribute )
        {
            $optional = 
                !isset( $this->attributes[$name] ) ||
                $this->attributes[$name]->optional ||
                $type->attributes[$name]->optional;

            if ( !isset( $this->attributes[$name] ) )
            {
                $this->attributes[$name] = $attribute;
            }
            $this->attributes[$name]->optional = $optional;
        }

        // Make attributes optional, which do not not occur in the merged type
        foreach ( $this->attributes as $name => $attribute )
        {
            if ( !isset( $type->attributes[$name] ) )
            {
                $this->attributes[$name]->optional = true;
            }
        }

        // Merge type automatons
        $this->regularExpression = null;
        $this->automaton->merge( $type->automaton );
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
            case 'name':
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
            case 'name':
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
        return $this->name;
    }
}

