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
 * Compares two attribute lists to contain the same attributes, or that 
 * attributes, which are only listed in one list, are at least optional.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaTypeEqualAttributeComparator extends slSchemaTypeAttributeComparator
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

        // Check that attributes only occuring in one of the attribute lists, 
        // are optional
        foreach ( $difference as $name )
        {
            if ( ( isset( $a->attributes[$name] ) &&
                   ( $a->attributes[$name]->optional !== true ) ) ||
                 ( isset( $b->attributes[$name] ) &&
                   ( $b->attributes[$name]->optional !== true ) ) )
            {
                return false;
            }
        }

        return true;
    }
}

