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
class slMainSoreConverterTests extends PHPUnit_Framework_TestCase
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

    public function testDisjunction()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testTripleDisjunction()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b' ) );
        $automaton->learn( array( 'c' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' ),
                new slRegularExpressionElement( 'c' )
            ),
            $regexp
        );
    }

    public function testConcatenation()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' )
            ),
            $regexp
        );
    }

    public function testTripleConcatenation()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b', 'c' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionElement( 'b' ),
                new slRegularExpressionElement( 'c' )
            ),
            $regexp
        );
    }

    public function testConcatenationOfDisjunction()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b1' ) );
        $automaton->learn( array( 'a', 'b2' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( 'b1' ),
                    new slRegularExpressionElement( 'b2' )
                )
            ),
            $regexp
        );
    }

    public function testDisjunctionOfConcatenation()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b', 'c' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionChoice(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( 'b' ),
                    new slRegularExpressionElement( 'c' )
                )
            ),
            $regexp
        );
    }

    public function testSelfLoop()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'a' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionRepeated(
                new slRegularExpressionElement( 'a' )
            ),
            $regexp
        );
    }

    public function testOptional()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b', 'c' ) );
        $automaton->learn( array( 'a', 'c' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence( array(
                new slRegularExpressionElement( 'a' ),
                new slRegularExpressionOptional(
                    new slRegularExpressionElement( 'b' )
                ),
                new slRegularExpressionElement( 'c' )
            ) ),
            $regexp
        );
    }

    public function testOptionalDouble()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a1', 'b', 'c' ) );
        $automaton->learn( array( 'a2', 'b', 'c' ) );
        $automaton->learn( array( 'a1', 'c' ) );
        $automaton->learn( array( 'a2', 'c' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence(
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( 'a1' ),
                    new slRegularExpressionElement( 'a2' )
                ),
                new slRegularExpressionSequence(
                    new slRegularExpressionOptional(
                        new slRegularExpressionElement( 'b' )
                    ),
                    new slRegularExpressionElement( 'c' )
                )
            ),
            $regexp
        );
    }

    public function testCrossLinkedCycles()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'c' ) );
        $automaton->learn( array( 'b', 'c' ) );
        $automaton->learn( array( 'c', 'c' ) );
        $automaton->learn( array( 'c', 'd' ) );
        $automaton->learn( array( 'd', 'c' ) );
        $automaton->learn( array( 'c', 'e' ) );
        $automaton->learn( array( 'e', 'c' ) );
        $automaton->learn( array( 'e', 'e' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            false,
            $regexp
        );
    }

    public function testFalseOnFail()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b' ) );
        $automaton->learn( array( 'b', 'a' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            false,
            $regexp
        );
    }

    public function testLearnAll()
    {
        $automaton = new slSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b', 'c' ) );
        $automaton->learn( array( 'a', 'c', 'b' ) );
        $automaton->learn( array( 'b', 'a', 'c' ) );
        $automaton->learn( array( 'b', 'c', 'a' ) );
        $automaton->learn( array( 'c', 'a', 'b' ) );
        $automaton->learn( array( 'c', 'b', 'a' ) );

        $converter = new slSoreConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            false,
            $regexp
        );
    }
}

