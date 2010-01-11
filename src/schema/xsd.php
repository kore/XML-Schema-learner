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
 * @todo:
 *      Refactor element handling:
 *
 *      The element objects should only contain their local name, not the full 
 *      path, no matter which type inferencer is used.
 *
 *      The element objects should additionally contain a type object, which 
 *      contains the type name. The automatons need to reference elements, 
 *      which do have an associated types. Therefore elements must get a unique 
 *      identifier in the application.
 *
 *      The types will be merged in thy type merger, so that multiple elements 
 *      may refer to the same type.
 *
 *      It will still not be possible to backtrack the locality of a type, 
 *      because a type does not know the elements it occurs in, and the 
 *      elements do not know the regular expressions they occur in.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slXsdSchema extends slSchema
{
    /**
     * Use type inferencer
     * 
     * @var slTypeInferencer
     */
    protected $typeInferencer;

    /**
     * Use type merger
     * 
     * @var slTypeMerger
     */
    protected $typeMerger;

    /**
     * Construct new schema class
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->typeInferencer = new slNameBasedTypeInferencer();
        $this->typeMerger     = new slNoTypeMerger();
    }

    /**
     * Set type inferencer
     * 
     * @param slTypeInferencer $typeInferencer 
     * @return void
     */
    public function setTypeInferencer( slTypeInferencer $typeInferencer )
    {
        $this->typeInferencer = $typeInferencer;
    }

    /**
     * Set type merger
     * 
     * @param slTypeMerger $typeMerger 
     * @return void
     */
    public function setTypeMerger( slTypeMerger $typeMerger )
    {
        $this->typeMerger = $typeMerger;
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

    /**
     * Get schema dependent simple type inferencer
     * 
     * @return slSimpleTypeInferencer
     */
    protected function getSimpleInferencer()
    {
        return new slPcdataSimpleTypeInferencer();
    }

    /**
     * Get regular expressions for learned schema
     *
     * Get an array of type -> regular expression associations for the learned 
     * schema.
     * 
     * @return array(slSchemaElement)
     */
    public function getTypes()
    {
        $this->types = $this->typeMerger->groupTypes( $this->types );
        return parent::getTypes();
    }
}

