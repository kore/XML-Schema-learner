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
 * Type merger.
 *
 * Abstract class, which offers the API for type mergers, which evaluate sets 
 * of types as equivalent.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slTypeMerger
{
    /**
     * Group equivalent types
     *
     * Receives an array with types => automaton pairs. Returns an identical 
     * array, or leaves the type association array, as it was.
     *
     * If the types are transformed it is especially important, that the types 
     * references, referenced in the automatons, itself, are also updated with 
     * the new type names.
     * 
     * @param array $types
     * @return array
     */
    abstract public function groupTypes( array $types );

    /**
     * Merge the types a and b into a single type
     *
     * Merges the types a and b. Only one merged type automaton will be left at 
     * either the name of a or b. Occurences on automatons of other types of a 
     * or b will be replaced by the new name.
     *
     * Returns a type => automaton array with the merged and updated type 
     * definitions.
     * 
     * @param array $types 
     * @param string $a 
     * @param string $b 
     * @return array
     */
    protected function mergeTypes( array $types, $a, $b )
    {
        $types[$a]->merge( $types[$b] );
        unset( $types[$b] );

        foreach ( $types as $type => $element )
        {
            $element->automaton->renameNode( $b, $a );
        }

        return $types;
    }
}

