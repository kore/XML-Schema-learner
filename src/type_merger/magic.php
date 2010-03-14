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
 * "Magic" type merger.
 *
 * Type merger, which merges all types, which contain the same child nodes in 
 * the child pattern and use either the same name, or use the same attributes.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slMagicTypeMerger extends slExactTypeMerger
{
    /**
     * Compares two automatons
     *
     * Compares two automatons and returns true, if they are equal by the 
     * implemented comparision metric. Returns true, if equal, and false 
     * otherwise.
     * 
     * @param slSchemaElement $a 
     * @param slSchemaElement $b 
     * @return bool
     */
    protected function equals( slSchemaElement $a, slSchemaElement $b )
    {
        if ( ( ( $nodes = $a->type->automaton->getNodes() ) === $b->type->automaton->getNodes() ) &&
             ( ( array_keys( $a->type->attributes ) === array_keys( $b->type->attributes ) ) ||
               ( $a->name === $b->name ) ) )
        {
            return true;
        }

        return false;
    }
}

