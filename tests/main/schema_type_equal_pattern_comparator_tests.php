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
class slMainSchemaTypeEqualPatternComparatorTests extends PHPUnit_Framework_TestCase
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
        'testTypePatternsSame'                => true,
        'testTypeEmptyPatternsSame'           => true,
        'testTypePatternsSameOptional'        => false,
        'testTypePatternsSameNodes'           => false,
        'testTypePatternsDifferentNodes'      => false,
        'testTypePatternsSingleDifferentNode' => false,
        'testTypePatternSubsumption'          => false,
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
        return new slSchemaTypeEqualPatternComparator();
    }

    public function testTypeEmptyPatternsSame()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternsSame()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternsSameOptional()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );
        $t1->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternsSameNodes()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );
        $t1->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'b', 'a', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternsDifferentNodes()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );
        $t1->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'c', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternsSingleDifferentNode()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'a', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }

    public function testTypePatternSubsumption()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->automaton->learn( array( '_s', '_e' ) );
        $t1->automaton->learn( array( '_s', 'a', '_e' ) );
        $t1->automaton->learn( array( '_s', 'a', 'b', '_e' ) );

        $t2 = new slSchemaType( 't2' );
        $t2->automaton->learn( array( '_s', 'a', '_e' ) );

        $comparator = $this->getComparator();
        $this->assertSame( $this->results[__FUNCTION__],  $comparator->compare( $t1, $t2 ) );
    }
}

