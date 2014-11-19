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
 * Trvial PCData simple type inferencer, which just considers all input as
 * PCData no matter what the actual string characteriscs might be.
 *
 * Omit all input data, because it does not influence the descision in any way
 * anyways.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slPcdataSimpleTypeInferencer extends slSimpleTypeInferencer
{
    /**
     * Store if the type has content at all
     * 
     * @var bool
     */
    protected $empty = true;

    /**
     * Learn the given string
     *
     * Record the given string, to inference a simple type from it.
     * 
     * @param string $string 
     * @return void
     */
    public function learnString( $string )
    {
        if ( strlen( $string ) )
        {
            $this->empty = false;
        }
    }

    /**
     * Inference type from learned strings
     *
     * Return a string type representation from all the strings, which have
     * been learned until now.
     * 
     * @return string
     */
    public function inferenceType()
    {
        // @todo: Return slPcdataSimpleType or similar
        return $this->empty ? 'empty' : 'PCDATA';
    }
}
 
