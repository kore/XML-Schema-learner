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
        $optimizer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionElement( 'a' )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionElement( 'a' ),
            $regexp
        );
    }

    public function testSingletonOptimizerDouble()
    {
        $optimizer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionElement( 'a' ),
            $regexp
        );
    }

    public function testSingletonOptimizerNoOptimzation()
    {
        $optimizer = new slRegularExpressionSingletonOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertFalse( $optimizer->optimize( $regexp ) );

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
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence();

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDouble()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice()
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerSingleEmpty()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionEmpty()
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmpty()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionChoice(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmptyOptional()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionOptional(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerOptional()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionOptional(
            new slRegularExpressionEmpty()
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerDoubleEmptyRepeated()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionRepeated(
                new slRegularExpressionEmpty()
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionEmpty(),
            $regexp
        );
    }

    public function testEmptyOptimizerNoOptimzation()
    {
        $optimizer = new slRegularExpressionEmptyOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionElement( 'a' )
        );

        $this->assertFalse( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' )
            ),
            $regexp
        );
    }

    public function testChoiceOptimizerSingle()
    {
        $optimizer = new slRegularExpressionChoiceOptimizer();

        $regexp = new slRegularExpressionChoice(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' )
            ),
            new slRegularExpressionElement( 'b' )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testChoiceOptimizerDouble()
    {
        $optimizer = new slRegularExpressionChoiceOptimizer();

        $regexp = new slRegularExpressionChoice(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' )
            ),
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'b' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testChoiceOptimizerTriple()
    {
        $optimizer = new slRegularExpressionChoiceOptimizer();

        $regexp = new slRegularExpressionChoice(
            new slRegularExpressionChoice(
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( 'a' )
                ),
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( 'b' )
                )
            ),
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'c' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' ),
                new slRegularExpressionElement( 'c' )
            ),
            $regexp
        );
    }

    public function testSequenceOptimizerSingle()
    {
        $optimizer = new slRegularExpressionSequenceOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' )
            ),
            new slRegularExpressionElement( 'b' )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testSequenceOptimizerDouble()
    {
        $optimizer = new slRegularExpressionSequenceOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' )
            ),
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'b' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testSequenceOptimizerTriple()
    {
        $optimizer = new slRegularExpressionSequenceOptimizer();

        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionSequence(
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( 'a' )
                ),
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( 'b' )
                )
            ),
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'c' )
            )
        );

        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );
        $this->assertTrue( $optimizer->optimize( $regexp ) );

        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' ),
                new slRegularExpressionElement( 'c' )
            ),
            $regexp
        );
    }
}

