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

require __DIR__ . '/automaton.php';
require __DIR__ . '/automaton/single_occurence.php';
require __DIR__ . '/automaton/weighted_single_occurence.php';
require __DIR__ . '/automaton/counting_single_occurence.php';

require __DIR__ . '/hidden_markov_model.php';
require __DIR__ . '/hidden_markov_model/k_occurence.php';
require __DIR__ . '/learn/baum_welch.php';

require __DIR__ . '/automaton/visitor.php';
require __DIR__ . '/automaton/visitor/dot.php';

require __DIR__ . '/converter.php';
require __DIR__ . '/converter/chare.php';
require __DIR__ . '/converter/echare.php';
require __DIR__ . '/converter/sore.php';

require __DIR__ . '/regular_expression.php';
require __DIR__ . '/regular_expression/element.php';
require __DIR__ . '/regular_expression/empty.php';
require __DIR__ . '/regular_expression/container.php';
require __DIR__ . '/regular_expression/multiple.php';
require __DIR__ . '/regular_expression/multiple/all.php';
require __DIR__ . '/regular_expression/multiple/choice.php';
require __DIR__ . '/regular_expression/multiple/sequence.php';
require __DIR__ . '/regular_expression/singular.php';
require __DIR__ . '/regular_expression/singular/optional.php';
require __DIR__ . '/regular_expression/singular/repeated.php';
require __DIR__ . '/regular_expression/singular/repeated_at_least_once.php';

require __DIR__ . '/regular_expression/optimizer.php';
require __DIR__ . '/regular_expression/optimizer/base.php';
require __DIR__ . '/regular_expression/optimizer/choice.php';
require __DIR__ . '/regular_expression/optimizer/empty.php';
require __DIR__ . '/regular_expression/optimizer/repetition.php';
require __DIR__ . '/regular_expression/optimizer/sequence.php';
require __DIR__ . '/regular_expression/optimizer/singleton.php';

require __DIR__ . '/regular_expression/visitor.php';
require __DIR__ . '/regular_expression/visitor/string.php';
require __DIR__ . '/regular_expression/visitor/dtd.php';
require __DIR__ . '/regular_expression/visitor/xml_schema.php';

require __DIR__ . '/schema.php';
require __DIR__ . '/schema/dtd.php';
require __DIR__ . '/schema/xsd.php';
require __DIR__ . '/schema/upsl.php';
require __DIR__ . '/schema/visitor.php';
require __DIR__ . '/schema/visitor/dtd.php';
require __DIR__ . '/schema/visitor/xml_schema.php';
require __DIR__ . '/schema/visitor/upsl.php';

require __DIR__ . '/schema/type.php';
require __DIR__ . '/schema/element.php';
require __DIR__ . '/schema/attribute.php';

require __DIR__ . '/simple_type_inferencer.php';
require __DIR__ . '/simple_type_inferencer/pcdata.php';

require __DIR__ . '/type_merger.php';
require __DIR__ . '/type_merger/no.php';
require __DIR__ . '/type_merger/exact.php';
require __DIR__ . '/type_merger/node_based.php';
require __DIR__ . '/type_merger/attribute_node_based.php';
require __DIR__ . '/type_merger/magic.php';

require __DIR__ . '/type_inferencer.php';
require __DIR__ . '/type_inferencer/name_based.php';
require __DIR__ . '/type_inferencer/full_path.php';
require __DIR__ . '/type_inferencer/k_local.php';

