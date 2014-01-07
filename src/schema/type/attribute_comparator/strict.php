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
 * Attrubute list comparator
 *
 * Compares two attribute lists to be the exactly same, even down to optional 
 * flags.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaTypeStrictAttributeComparator extends slSchemaTypeAttributeComparator
{
    /**
     * Compare attributes
     *
     * Returns true, if the attributes are the same, by definition of the used 
     * comparision algorithm.
     * 
     * @param slSchemaType $a 
     * @param slSchemaType $b 
     * @return bool
     */
    public function compare( slSchemaType $a, slSchemaType $b )
    {
        $difference = array_diff(
            array_keys( $a->attributes ),
            array_keys( $b->attributes )
        );

        // In strict mode, every attribute occuring in only of the types will 
        // make the comparison fail.
        if ( count( $difference ) )
        {
            return false;
        }

        // Ensure all optional flags are the same in both attribute arrays.
        foreach ( $a->attributes as $name => $attribute )
        {
            if ( $a->attributes[$name]->optional !== $b->attributes[$name]->optional )
            {
                return false;
            }
        }

        return true;
    }
}

