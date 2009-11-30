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
 * Basic converter for single occurence automatons to regular expressions
 *
 * CRX-Algorithm implemented like described in:
 *
 * "Inference of Concise DTDs from XML Data",
 * by
 *  - Geert Jan Bex
 *  - Frank Neven
 *  - Thomas Schwentick
 *  - Karl Tuyls
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slChareConverter extends slConverter
{
    /**
     * Array with nodes and their associated regular expressions
     * 
     * @var array
     */
    protected $nodes = array();

    /**
     * VConvert automaton to regular expression
     * 
     * @param slCountingSingleOccurenceAutomaton $automaton 
     * @return slRegularExpression
     */
    public function convertAutomaton( slCountingSingleOccurenceAutomaton $automaton )
    {
        // Build transitive reflexive closures of doubly linked nodes

        // Sort nodes topologically

        // Build regular expression from sorted node sets
    }
}

