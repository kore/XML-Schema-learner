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
            new slRegularExpressionChoice( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionSequence( array( 'b' ) ),
            ) ),
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
            new slRegularExpressionChoice( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionSequence( array( 'b' ) ),
                new slRegularExpressionSequence( array( 'c' ) ),
            ) ),
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
            new slRegularExpressionSequence( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionSequence( array( 'b' ) ),
            ) ),
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
            new slRegularExpressionSequence( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionSequence( array( 'b' ) ),
                new slRegularExpressionSequence( array( 'c' ) ),
            ) ),
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
            new slRegularExpressionSequence( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionChoice( array(
                    new slRegularExpressionSequence( array( 'b1' ) ),
                    new slRegularExpressionSequence( array( 'b2' ) ),
                ) ),
            ) ),
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
            new slRegularExpressionChoice( array(
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionSequence( array(
                    new slRegularExpressionSequence( array( 'b' ) ),
                    new slRegularExpressionSequence( array( 'c' ) ),
                ) ),
            ) ),
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
            new slRegularExpressionRepeated( array(
                new slRegularExpressionSequence( array( 'a' ) ),
            ) ),
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
                new slRegularExpressionSequence( array( 'a' ) ),
                new slRegularExpressionOptional( array(
                    new slRegularExpressionSequence( array( 'b' ) ),
                ) ),
                new slRegularExpressionSequence( array( 'c' ) ),
            ) ),
            $regexp
        );
    }
}

