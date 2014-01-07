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
 * of types as equivalent.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slNoTypeMerger extends slTypeMerger
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
        return $types;
    }
}

