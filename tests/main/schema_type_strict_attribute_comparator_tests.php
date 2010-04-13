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
class slMainSchemaTypeStrictAttributeComparatorTests extends PHPUnit_Framework_TestCase
{
    /**
     * Expected test results.
     *
     * This array, together with the getComparator() method simulates a two 
     * dimensional inheritence based data provider.
     * 
     * @var array
     */
    protected $results = array(
        'testTypeAttributesSame'              => true,
        'testTypeAttributesSameOptional'      => true,
        'testTypeAttributesHalfOptional'      => false,
        'testTypeAttributesDifferent'         => false,
        'testTypeAttributesOptionalDifferent' => false,
    );

    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    protected function getComparator()
    {
        return new slSchemaTypeStrictAttributeComparator();
    }

    public function testTypeAttributesSame()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypeAttributesSameOptional()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '42',
        ) );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '42',
        ) );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypeAttributesHalfOptional()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '42',
        ) );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypeAttributesDifferent()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att2' => '42',
        ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypeAttributesOptionalDifferent()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array() );
        $t1->learnAttributes( array(
            'att1' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array() );
        $t2->learnAttributes( array(
            'att2' => '42',
        ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }
}

