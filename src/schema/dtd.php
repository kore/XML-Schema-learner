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
 * Class representing a DTD schema.
 *
 * DTD schemas can only learn each element name as a single type, so that this 
 * schema uses the simple element name based type inferencer.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slDtdSchema extends slSchema
{
    /**
     * Use type inferencer
     * 
     * @var slNameBasedTypeInferencer
     */
    protected $typeInferencer;

    /**
     * Construct new schema class
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->typeInferencer = new slNameBasedTypeInferencer();
    }

    /**
     * Inference type from DOMElement
     * 
     * @param DOMElement $element 
     * @return void
     */
    protected function inferenceType( DOMElement $element )
    {
        return $this->typeInferencer->inferenceType( $element );
    }
}

