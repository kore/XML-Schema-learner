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
 * Full path type inferencer
 *
 * Uses the full ancestor path to an element as the type identifier.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slFullPathTypeInferencer extends slNameBasedTypeInferencer
{
    /**
     * Inference a type from element
     *
     * Inference a string type from the given DOMELement.
     * 
     * @param DOMELement $element 
     * @return string
     */
    public function inferenceType( DOMELement $element )
    {
        $elements = array();
        do {
            $elements[] = parent::inferenceType( $element );
        } while( ( $element = $element->parentNode ) instanceof DOMElement );

        return implode( '/', array_reverse( $elements ) );
    }
}

