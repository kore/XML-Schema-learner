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

require __DIR__ . '/type_inferencer.php';
require __DIR__ . '/type_inferencer/name_based.php';

require __DIR__ . '/automaton.php';
require __DIR__ . '/automaton/single_occurence.php';

require __DIR__ . '/regular_expression.php';
require __DIR__ . '/regular_expression/sequence.php';
require __DIR__ . '/regular_expression/choice.php';
require __DIR__ . '/regular_expression/optional.php';
require __DIR__ . '/regular_expression/repeated.php';

require __DIR__ . '/converter.php';
require __DIR__ . '/converter/sore.php';

