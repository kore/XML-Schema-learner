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
class slMainSingleOccurenceAutomatonTests extends slMainAutomatonTests
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

    /**
     * Return automaton implementation to test
     * 
     * @return slSingleOccurenceAutomaton
     */
    protected function getAutomaton()
    {
        return new slSingleOccurenceAutomaton();
    }

    public function testLearnEmptySequence()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array() );
        $this->assertEquals( array(), array_values( $automaton->getNodes() ) );
    }

    public function testLearnSingle()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a' ) );
        $this->assertEquals( array( 'a' ), array_values( $automaton->getNodes() ) );
    }

    public function testLearnTriple()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a', 'b', 'c' ) );
        $this->assertEquals( array( 'a', 'b', 'c' ), array_values( $automaton->getNodes() ) );
        $this->assertEquals( array( 'b' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'b' ) );
        $this->assertEquals( array(), $automaton->getOutgoing( 'c' ) );
    }

    public function testTwoTuples()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a', 'b' ) );
        $automaton->learn( array( 'b', 'c' ) );
        $this->assertEquals( array( 'a', 'b', 'c' ), array_values( $automaton->getNodes() ) );
        $this->assertEquals( array( 'b' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'b' ) );
        $this->assertEquals( array(), $automaton->getOutgoing( 'c' ) );
    }

    public function testCircle()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a', 'b' ) );
        $automaton->learn( array( 'b', 'c' ) );
        $automaton->learn( array( 'c', 'a' ) );
        $this->assertEquals( array( 'a', 'b', 'c' ), array_values( $automaton->getNodes() ) );
        $this->assertEquals( array( 'b' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'b' ) );
        $this->assertEquals( array( 'a' ), $automaton->getOutgoing( 'c' ) );
    }
}

