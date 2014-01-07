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
            $elements = $merger->groupTypes( $elements )
        );

        foreach ( $elements as $element )
        {
            foreach ( $element->type->automaton->getNodes() as $node )
            {
                $this->assertTrue(
                    $node === 0 ||
                    $node === 1 ||
                    $node instanceof slSchemaAutomatonNode
                );
            }
        }

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
            $elements = $merger->groupTypes( $elements )
        );

        foreach ( $elements as $element )
        {
            foreach ( $element->type->automaton->getNodes() as $node )
            {
                $this->assertTrue(
                    $node === 0 ||
                    $node === 1 ||
                    $node instanceof slSchemaAutomatonNode
                );
            }
        }

        $this->assertEquals(
            array(
                'b_type'  => 'a_type',
                'c2_type' => 'c1_type',
            ),
            $merger->getTypeMapping()
        );
    }

    public function testMergeRecursiveTypes()
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

        $rootType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'a_type' ), 1 ) );
        $rootType->learnAttributes( array() );

        $aType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'b', 'b_type' ), 1 ) );
        $aType->learnAttributes( array() );

        $bType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'a_type' ), 1 ) );
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
            $elements = $merger->groupTypes( $elements )
        );

        foreach ( $elements as $element )
        {
            foreach ( $element->type->automaton->getNodes() as $node )
            {
                $this->assertTrue(
                    $node === 0 ||
                    $node === 1 ||
                    $node instanceof slSchemaAutomatonNode
                );
            }
        }

        $this->assertEquals(
            array(
                'b_type' => 'root_type',
            ),
            $merger->getTypeMapping()
        );
    }

    public function testMergeComplexTypes()
    {
        $elements = array(
            'r_type' => new slSchemaElement(
                'root',
                $rType = new slSchemaType( 'r_type' )
            ),
            'ra_type' => new slSchemaElement(
                'a',
                $raType = new slSchemaType( 'ra_type' )
            ),
            'rb_type' => new slSchemaElement(
                'b',
                $rbType = new slSchemaType( 'rb_type' )
            ),
            'rba_type' => new slSchemaElement(
                'a',
                $rbaType = new slSchemaType( 'rba_type' )
            ),
            'rbc_type' => new slSchemaElement(
                'c',
                $rbcType = new slSchemaType( 'rbc_type' )
            ),
            'rbcd_type' => new slSchemaElement(
                'd',
                $rbcdType = new slSchemaType( 'rbcd_type' )
            ),
            'rc_type' => new slSchemaElement(
                'c',
                $rcType = new slSchemaType( 'g_type' )
            ),
            'rca_type' => new slSchemaElement(
                'a',
                $rcaType = new slSchemaType( 'rca_type' )
            ),
        );

        $rType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'ra_type' ), new slSchemaAutomatonNode( 'b', 'rb_type' ), new slSchemaAutomatonNode( 'c', 'rc_type' ), 1 ) );
        $rType->learnAttributes( array() );

        $raType->automaton->learn( array( 0, 1 ) );
        $raType->learnAttributes( array() );

        $rbType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'rba_type' ), new slSchemaAutomatonNode( 'c', 'rbc_type' ), 1 ) );
        $rbType->learnAttributes( array() );

        $rbaType->automaton->learn( array( 0, 1 ) );
        $rbaType->learnAttributes( array() );

        $rbcType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'd', 'rbcd_type' ), 1 ) );
        $rbcType->learnAttributes( array() );

        $rbcdType->automaton->learn( array( 0, 1 ) );
        $rbcdType->learnAttributes( array() );

        $rcType->automaton->learn( array( 0, new slSchemaAutomatonNode( 'a', 'rca_type' ), 1 ) );
        $rcType->learnAttributes( array() );

        $rcaType->automaton->learn( array( 0, 1 ) );
        $rcaType->learnAttributes( array() );

        $merger = new slConfigurableTypeMerger(
            new slSchemaTypeSubsumingPatternComparator(),
            new slSchemaTypeEqualAttributeComparator()
        );

        $this->assertEquals(
            array(
                'r_type'   => new slSchemaElement( 'root', $rType ),
                'ra_type'  => new slSchemaElement( 'a', $raType ),
                'rb_type'  => new slSchemaElement( 'b', $rbType ),
                'rbc_type' => new slSchemaElement( 'c', $rbcType ),
                'rc_type'  => new slSchemaElement( 'c', $rcType ),
            ),
            $elements = $merger->groupTypes( $elements )
        );

        foreach ( $elements as $element )
        {
            foreach ( $element->type->automaton->getNodes() as $node )
            {
                $this->assertTrue(
                    $node === 0 ||
                    $node === 1 ||
                    $node instanceof slSchemaAutomatonNode
                );
            }
        }

        $this->assertEquals(
            array(
                'rba_type'  => 'ra_type',
                'rbcd_type' => 'ra_type',
                'rca_type'  => 'ra_type',
            ),
            $merger->getTypeMapping()
        );
    }
}

