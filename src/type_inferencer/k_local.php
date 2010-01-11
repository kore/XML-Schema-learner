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
 * K-local type inferencer
 *
 * Uses a configured ancestor depth to create type identifiers with the 
 * configured ancestor dependency.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slKLocalTypeInferencer extends slNameBasedTypeInferencer
{
    /**
     * K, the ancestor dependency of a type.
     * 
     * @var int
     */
    protected $depth;

    /**
     * Construct from k, the ancestor dependency depth
     * 
     * @param int $k 
     * @return void
     */
    public function __construct( $k )
    {
        $this->depth = (int) $k;
    }

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
        for ( $i = 0; $i <= $this->depth; ++$i )
        {
            $elements[] = parent::inferenceType( $element );
            
            if ( $element->parentNode instanceof DOMDOcument )
            {
                break;
            }

            $element = $element->parentNode;
        }

        return implode( '/', array_reverse( $elements ) );
    }
}

