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
 * Class representing an element in an inferenced schema
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaElement
{
    /**
     * Name space of the element
     *
     * @todo: Yet unused, since we only care for the structural properties of 
     * XSDs for now.
     * 
     * @var string
     */
    protected $namespace = 'http://example.org/namespace';

    /**
     * Name of the element
     * 
     * @var string
     */
    protected $name;

    /**
     * Type of the schema element.
     *
     * The type is a string sufficiently unique for the types occuring in the
     * schema.
     * 
     * @var slSchemaType
     */
    protected $type;

    /**
     * Construct a schema element from a string type representation
     * 
     * @param string $name 
     * @param slSchemaType $type 
     * @return void
     */
    public function __construct( $name, slSchemaType $type )
    {
        $this->name = $name;
        $this->type = $type;
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
            case 'name':
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
            case 'type':
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

