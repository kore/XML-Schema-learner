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
 * Regular expression representation base class
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slRegularExpressionMultiple extends slRegularExpressionContainer implements ArrayAccess // , Iterator
{
    /**
     * Children in regular expression syntax tree
     * 
     * @var array
     */
    protected $children = array();

    /**
     * Construct regular expression
     *
     * Optionally from a set of given child nodes in the regular expression 
     * syntax tree.
     *
     * Accepts any parameter count as subexpressions.
     * 
     * @param slRegularExpression $children 
     * @return void
     */
    public function __construct()
    {
        $this->setChildren( $this->flattenArray( func_get_args() ) );
    }

    /**
     * Flatten a (deeply) nested array into a one-dimensional array.
     *
     * Preserves all contents of the input array, but just flattens the array 
     * into one dimension, maintaining the original order of the elements.
     * 
     * @param array $arguments 
     * @return array
     */
    protected function flattenArray( array $arguments )
    {
        $flattened = array();
        foreach ( $arguments as $arg )
        {
            if ( is_array( $arg ) )
            {
                $flattened = array_merge( $flattened, $this->flattenArray( $arg ) );
            }
            else
            {
                $flattened[] = $arg;
            }
        }
        
        return $flattened;
    }

    /**
     * Set children
     * 
     * @param array $children 
     * @return void
     */
    public function setChildren( array $children )
    {
        $this->children = array();
        foreach ( $children as $child )
        {
            if ( !$child instanceof slRegularExpression )
            {
                throw new RuntimeException( 'Invalid regular expression child added.' );
            }

            $this->children[] = $child;
        }
    }

    /**
     * Get children
     * 
     * @return array(slRegularExpression)
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns if the given offset exists.
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * children of this object as if it was an array.
     * 
     * @param int $key
     * @return bool
     */
    public function offsetExists( $key )
    {
        return isset( $this->children[$key] );
    }

    /**
     * Returns the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * children of this object as if it was an array. 
     * 
     * @param int $key
     * @return mixed
     *
     * @throws ezcBasePropertyNotFoundException
     *         If no childrenset with identifier exists
     */
    public function offsetGet( $key )
    {
        if ( !isset( $this->children[$key] ) )
        {
            // @todo: Throw proper exception
            throw new RuntimeException( "$key does not exist." );
        }

        return $this->children[$key];
    }

    /**
     * Set the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     * children of this object as if it was an array. 
     *
     * @param int $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet( $key, $value )
    {
        $this->children[$key] = $value;
    }

    /**
     * Unset the element with the given offset. 
     *
     * This method is part of the ArrayAccess interface to allow access to the
     *
     * Is not permitted for this stack implementation.
     * 
     * @param string $key
     * @return void
     *
     * @throws ezcBaseValueException
     *         Setting values is not allowed
     */
    public function offsetUnset( $key )
    {
        if ( !isset( $this->children[$key] ) )
        {
            // @todo: Throw proper exception
            throw new RuntimeException( "$key does not exist." );
        }

        unset( $this->children[$key] );
    }

    /**
     * Returns the number of children
     *
     * This method is part of the Countable interface to allow the usage of
     * PHP's count() function to check how many childrensets exist.
     *
     * @return int
     */
    public function count()
    {
        return count( $this->children );
    }
}

