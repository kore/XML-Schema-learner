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
 * Class representing an attribute in an inferenced schema
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaAttribute
{
    /**
     * Name of the attribute
     *
     * The name is a string sufficiently unique for the types occuring in the
     * element.
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
    protected $simpleTypeInferencer;

    /**
     * Flag wheather the attribut is optional
     * 
     * @var bool
     */
    protected $optional = false;

    /**
     * Construct an attribute from its name and a simpleTypeInferencer
     * 
     * @param string $name 
     * @param slSimpleTypeInferencer $simpleTypeInferencer 
     * @return void
     */
    public function __construct( $name, slSimpleTypeInferencer $simpleTypeInferencer )
    {
        $this->name                 = $name;
        $this->simpleTypeInferencer = $simpleTypeInferencer;
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
            case 'optional':
            case 'simpleTypeInferencer':
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
            case 'optional':
                $this->$property = $value;
                break;

            default:
                throw new RuntimeException( "Property '$property' is not available." );
        }
    }
}

