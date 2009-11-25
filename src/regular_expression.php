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
 * Regular expression representation base class
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slRegularExpression
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
     * syntax tree
     * 
     * @param array $children 
     * @return void
     */
    public function __construct( array $children = array() )
    {
        $this->setChildren( $children );
    }

    /**
     * Set children
     * 
     * @param array $children 
     * @return void
     */
    public function setChildren( array $children )
    {
        $this->children = $children;
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
}

