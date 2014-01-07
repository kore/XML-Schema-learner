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
class slVisitorRegularExpressionDtdTests extends PHPUnit_Framework_TestCase
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

    public function testVisitEmpty()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertSame(
            '',
            $visitor->visit(
                new slRegularExpressionEmpty()
            )
        );
    }

    public function testVisitElement()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertSame(
            'a',
            $visitor->visit(
                new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
            )
        );
    }

    public function testVisitNumericElement()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertSame(
            '23',
            $visitor->visit(
                new slRegularExpressionElement( new slSchemaAutomatonNode( 23, 23 ) )
            )
        );
    }

    public function testVisitSequence()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( a, b )',
            $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                )
            )
        );
    }

    public function testVisitChoice()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( a | b )',
            $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                )
            )
        );
    }

    public function testVisitAll()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( ( a, b, c ) | ( a, c, b ) | ( b, a, c ) | ( b, c, a ) | ( c, a, b ) | ( c, b, a ) )',
            $visitor->visit(
                new slRegularExpressionAll(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'c', 'c' ) )
                )
            )
        );
    }

    public function testVisitOptional()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            'a?',
            $visitor->visit(
                new slRegularExpressionOptional(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                )
            )
        );
    }

    public function testVisitRepeated()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            'a*',
            $visitor->visit(
                new slRegularExpressionRepeated(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                )
            )
        );
    }

    public function testVisitStackedSequence()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( ( a ), ( b ) )',
            $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                    )
                )
            )
        );
    }

    public function testVisitConcatenationOfDisjunction()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( ( a ), ( b1 | b2 ) )',
            $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionChoice(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b1', 'b1' ) ),
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b2', 'b2' ) )
                    )
                )
            )
        );
    }

    public function testDisjunctionOfConcatenation()
    {
        $visitor = new slRegularExpressionDtdVisitor();
        $this->assertEquals(
            '( ( a ) | ( ( b ), ( c ) ) )',
            $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                        ),
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( new slSchemaAutomatonNode( 'c', 'c' ) )
                        )
                    )
                )
            )
        );
    }
}

