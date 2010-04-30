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

/*
 * Require environment file
 */
require_once __DIR__ . '/../src/environment.php';

/**
 * Require tests
 */
require 'main/automaton_tests.php';
require 'main/hmm_tests.php';
require 'main/ko_hmm_tests.php';
require 'main/baum_welch_tests.php';
require 'main/single_occurence_automaton_tests.php';
require 'main/weighted_single_occurence_automaton_tests.php';
require 'main/counting_single_occurence_automaton_tests.php';
require 'main/type_automaton_tests.php';
require 'main/type_inferencer_tests.php';
require 'main/type_merger_tests.php';
require 'main/sore_converter_tests.php';
require 'main/chare_converter_tests.php';
require 'main/e_chare_converter_tests.php';
require 'main/regular_expression_tests.php';
require 'main/regular_expression_optimizer_tests.php';
require 'main/regular_expression_optimizer_manager_tests.php';
require 'main/schema_tests.php';
require 'main/schema_type_tests.php';
require 'main/schema_type_strict_attribute_comparator_tests.php';
require 'main/schema_type_same_attribute_comparator_tests.php';
require 'main/schema_type_equal_attribute_comparator_tests.php';
require 'main/schema_type_merge_attribute_comparator_tests.php';
require 'main/schema_type_equal_pattern_comparator_tests.php';
require 'main/schema_type_reduce_pattern_comparator_tests.php';
require 'main/schema_type_node_based_pattern_comparator_tests.php';
require 'main/schema_type_subsumption_pattern_comparator_tests.php';
require 'main/schema_element_tests.php';
require 'main/schema_attribute_tests.php';
require 'main/schema_dtd_tests.php';

/**
 * General root test suite
 */
class slMainTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'SchemaLearner - Main' );

        $this->addTest( slMainAutomatonTests::suite() );
        $this->addTest( slMainHiddenMarkovModelTests::suite() );
        $this->addTest( slMainKOccurenceHiddenMarkovModelTests::suite() );
        $this->addTest( slMainBaumWelchTests::suite() );
        $this->addTest( slMainSingleOccurenceAutomatonTests::suite() );
        $this->addTest( slMainWeightedSingleOccurenceAutomatonTests::suite() );
        $this->addTest( slMainCountingSingleOccurenceAutomatonTests::suite() );
        $this->addTest( slMainTypeAutomatonTests::suite() );
        $this->addTest( slMainTypeInferencerTests::suite() );
        $this->addTest( slMainTypeMergerTests::suite() );
        $this->addTest( slMainSoreConverterTests::suite() );
        $this->addTest( slMainChareConverterTests::suite() );
        $this->addTest( slMainEChareConverterTests::suite() );
        $this->addTest( slMainRegularExpressionTests::suite() );
        $this->addTest( slMainRegularExpressionOptimizerTests::suite() );
        $this->addTest( slMainRegularExpressionOptimizerManagerTests::suite() );
        $this->addTest( slMainSchemaTests::suite() );
        $this->addTest( slMainSchemaTypeTests::suite() );
        $this->addTest( slMainSchemaTypeStrictAttributeComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeSameAttributeComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeEqualAttributeComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeMergeAttributeComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeEqualPatternComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeReducePatternComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeNodeBasedPatternComparatorTests::suite() );
        $this->addTest( slMainSchemaTypeSubsumingPatternComparatorTests::suite() );
        $this->addTest( slMainSchemaElementTests::suite() );
        $this->addTest( slMainSchemaAttributeTests::suite() );
        $this->addTest( slMainSchemaDtdTests::suite() );
    }

    /**
     * Return test suite
     * 
     * @return slTestSuite
     */
    public static function suite()
    {
        return new slMainTestSuite( __CLASS__ );
    }
}
