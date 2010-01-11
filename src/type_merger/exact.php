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
class slExactTypeMerger extends slTypeMerger
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
    public function groupTypes( array $types )
    {
        do {
            $changed   = false;
            $typeCount = count( $types );
            $typeIndex = array_keys( $types );

            for ( $i = 0; $i < $typeCount; ++$i )
            {
                for ( $j = $i + 1; $j < $typeCount; ++$j )
                {
                    if ( $this->equals( $types[$typeIndex[$i]], $types[$typeIndex[$j]] ) )
                    {
                        $types   = $this->mergeTypes( $types, $typeIndex[$i], $typeIndex[$j] );
                        $changed = true;
                        break 2;
                    }
                }
            }

        } while ( $changed );

        return $types;
    }

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
        // If there are simple types, check first if those are compatible
        if ( $a->simpleTypeInferencer->inferenceType() !== $b->simpleTypeInferencer->inferenceType() )
        {
            return false;
        }

        if ( ( $nodes = $a->automaton->getNodes() ) !== $b->automaton->getNodes() )
        {
            return false;
        }

        foreach ( $nodes as $node )
        {
            if ( ( $a->automaton->getOutgoing( $node ) !== $b->automaton->getOutgoing( $node ) ) ||
                 ( $a->automaton->getIncoming( $node ) !== $b->automaton->getIncoming( $node ) ) )
            {
                return false;
            }
        }

        return true;
    }
}

