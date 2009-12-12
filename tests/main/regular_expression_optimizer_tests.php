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
class slMainRegularExpressionOptimizerTests extends PHPUnit_Framework_TestCase
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

    public function testSingletonOptimizerSingle()
    {
        $optimzer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionElement( 'a' )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionElement( 'a' ),
            $regexp
        );
    }

    public function testSingletonOptimizerDouble()
    {
        $optimzer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' )
            )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionElement( 'a' ),
            $regexp
        );
    }

    public function testSingletonOptimizerNoOptimzation()
    {
        $optimzer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertFalse( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testEmptyOptimizerSingle()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence();

        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDouble()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice()
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerSingleEmpty()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionEmpty()
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmpty()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmptyOptional()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionOptional(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmptyRepeated()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionRepeated(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimzer->optimize( $regexp ) );
        $this->assertTrue( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerNoOptimzation()
    {
        $optimzer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionElement( 'a' )
        );

        $this->assertFalse( $optimzer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' )
            ),
            $regexp
        );
    }
}

