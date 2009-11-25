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
class slMainAutomatonTests extends PHPUnit_Framework_TestCase
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

    public function testFreshAutomaton()
    {
        $automaton = new slAutomaton();
        $this->assertEquals( array(), $automaton->getNodes() );
    }

    public function testCreateEdge()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'a', 'b' ), $automaton->getNodes() );
    }

    public function testIncoming()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'a' ), $automaton->getIncoming( 'b' ) );
    }

    public function testNoIncoming()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array(), $automaton->getIncoming( 'a' ) );
    }

    public function testOutgoing()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'b' ), $automaton->getOutgoing( 'a' ) );
    }

    public function testNoOutgoing()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array(), $automaton->getOutgoing( 'b' ) );
    }

    public function testMultipleEdges()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'c' );
        $automaton->addEdge( 'b', 'c' );

        $this->assertEquals( array( 'a', 'b', 'c' ), $automaton->getNodes() );
        $this->assertEquals( array( 'b', 'c' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'a', 'b' ), $automaton->getIncoming( 'c' ) );
    }

    public function testRemoveNode()
    {
        $automaton = new slAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'c' );
        $automaton->addEdge( 'b', 'c' );
        $automaton->removeNode( 'b' );

        $this->assertEquals( array( 'a', 'c' ), $automaton->getNodes() );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'a' ), $automaton->getIncoming( 'c' ) );
    }
}

