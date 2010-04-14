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
 * Test class
 */
class slMainTypeMergerTests extends PHPUnit_Framework_TestCase
{
    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public function testMergeEmptyTypes()
    {
        $elements = array(
            'root_type' => new slSchemaElement(
                'root',
                $rootType = new slSchemaType( 'root_type' )
            ),
            'a_type' => new slSchemaElement(
                'a',
                $aType = new slSchemaType( 'a_type' )
            ),
            'b_type' => new slSchemaElement(
                'b',
                $bType = new slSchemaType( 'b_type' )
            ),
        );

        $rootType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'a_type' ), new slSchemaAutomatonNode( 'b', 'b_type' ), 1 ) );
        $rootType->learnAttributes( array() );

        $aType->automaton->learn( array( 0, 1 ) );
        $aType->learnAttributes( array() );

        $bType->automaton->learn( array( 0, 1 ) );
        $bType->learnAttributes( array() );

        $merger = new slConfigurableTypeMerger(
            new slSchemaTypeSubsumingPatternComparator(),
            new slSchemaTypeEqualAttributeComparator()
        );

        $this->assertEquals(
            array(
                'root_type' => new slSchemaElement( 'root', $rootType ),
                'a_type'    => new slSchemaElement( 'a', $aType ),
            ),
            $merger->groupTypes( $elements )
        );

        $this->assertEquals(
            array(
                'b_type' => 'a_type',
            ),
            $merger->getTypeMapping()
        );
    }

    public function testMergeIndirectSimpleTypes()
    {
        $elements = array(
            'root_type' => new slSchemaElement(
                'root',
                $rootType = new slSchemaType( 'root_type' )
            ),
            'a_type' => new slSchemaElement(
                'a',
                $aType = new slSchemaType( 'a_type' )
            ),
            'b_type' => new slSchemaElement(
                'b',
                $bType = new slSchemaType( 'b_type' )
            ),
            'c1_type' => new slSchemaElement(
                'c',
                $c1Type = new slSchemaType( 'c1_type' )
            ),
            'c2_type' => new slSchemaElement(
                'c',
                $c2Type = new slSchemaType( 'c2_type' )
            ),
        );

        $rootType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'a_type' ), new slSchemaAutomatonNode( 'b', 'b_type' ), 1 ) );
        $rootType->learnAttributes( array() );

        $aType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'c', 'c1_type' ), 1 ) );
        $aType->learnAttributes( array() );

        $bType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'c', 'c2_type' ), 1 ) );
        $bType->learnAttributes( array() );

        $c1Type->automaton->learn( array( 0, 1 ) );
        $c1Type->learnAttributes( array() );

        $c2Type->automaton->learn( array( 0, 1 ) );
        $c2Type->learnAttributes( array() );

        $merger = new slConfigurableTypeMerger(
            new slSchemaTypeSubsumingPatternComparator(),
            new slSchemaTypeEqualAttributeComparator()
        );

        $this->assertEquals(
            array(
                'root_type' => new slSchemaElement( 'root', $rootType ),
                'a_type'    => new slSchemaElement( 'a', $aType ),
                'c1_type'    => new slSchemaElement( 'c', $c1Type ),
            ),
            $merger->groupTypes( $elements )
        );

        $this->assertEquals(
            array(
                'b_type'  => 'a_type',
                'c2_type' => 'c1_type',
            ),
            $merger->getTypeMapping()
        );
    }
}

