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
 * Type merger.
 *
 * Abstract class, which offers the API for type mergers, which evaluate sets 
 * of elements as equivalent.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slTypeMerger
{
    /**
     * Surjective mapping of merged types.
     * 
     * @var array
     */
    protected $typeMapping = array();

    /**
     * Group equivalent elements
     *
     * Receives an array with elements => automaton pairs. Returns an identical 
     * array, or leaves the type association array, as it was.
     *
     * If the elements are transformed it is especially important, that the elements 
     * references, referenced in the automatons, itself, are also updated with 
     * the new type names.
     * 
     * @param array $elements
     * @return array
     */
    abstract public function groupTypes( array $elements );

    /**
     * Merge the elements a and b into a single type
     *
     * Merges the elements a and b. Only one merged type automaton will be left at 
     * either the name of a or b. Occurences on automatons of other elements of a 
     * or b will be replaced by the new name.
     * 
     * @param array $elements 
     * @param string $a 
     * @param string $b 
     * @return array
     */
    protected function mergeTypes( array $elements, $a, $b )
    {
        $mergedType = $elements[$a]->type;
        $mergedType->merge( $elements[$b]->type );

        $this->typeMapping[$b] = $a;

        $elements[$a]->type = $mergedType;
        $elements[$b]->type = $mergedType;
    }

    /**
     * Return type mapping
     *
     * Returns a surjective mapping of types, which has been merged.
     * 
     * @return array
     */
    public function getTypeMapping()
    {
        return $this->typeMapping;
    }
}

