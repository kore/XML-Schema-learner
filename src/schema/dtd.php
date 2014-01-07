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
     * Set type inferencer
     * 
     * @param slTypeInferencer $typeInferencer 
     * @return void
     */
    public function setTypeInferencer( slTypeInferencer $typeInferencer )
    {
        if ( !$typeInferencer instanceof slNameBasedTypeInferencer )
        {
            throw new RuntimeException( 'DTD only works with the slNameBasedTypeInferencer.' );
        }

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
        if ( !$typeMerger instanceof slNoTypeMerger )
        {
            throw new RuntimeException( 'DTD only works with the slNoTypeMerger.' );
        }

        $this->typeMerger = $typeMerger;
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
}

