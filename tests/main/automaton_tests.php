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

    /**
     * Return automaton implementation to test
     * 
     * @return slSingleOccurenceAutomaton
     */
    protected function getAutomaton()
    {
        return new slAutomaton();
    }

    public function testFreshAutomaton()
    {
        $automaton = $this->getAutomaton();
        $this->assertEquals( array(), $automaton->getNodes() );
    }

    public function testCreateNode()
    {
        $automaton = $this->getAutomaton();
        $automaton->addNode( 'a' );
        $this->assertEquals( array( 'a' ), $automaton->getNodes() );
    }

    public function testCreateEdge()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'a', 'b' ), $automaton->getNodes() );
    }

    public function testIncoming()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'a' ), $automaton->getIncoming( 'b' ) );
    }

    public function testNoIncoming()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array(), $automaton->getIncoming( 'a' ) );
    }

    public function testOutgoing()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array( 'b' ), $automaton->getOutgoing( 'a' ) );
    }

    public function testNoOutgoing()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $this->assertEquals( array(), $automaton->getOutgoing( 'b' ) );
    }

    public function testMultipleEdges()
    {
        $automaton = $this->getAutomaton();
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
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'c' );
        $automaton->addEdge( 'b', 'c' );
        $automaton->removeNode( 'b' );

        $this->assertEquals( array( 'a', 'c' ), $automaton->getNodes() );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'a' ), $automaton->getIncoming( 'c' ) );
    }

    public function testRemoveUnknownNode()
    {
        $automaton = $this->getAutomaton();
        $this->assertFalse( $automaton->removeNode( 'unknown' ) );
    }

    public function testRemoveEdge()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'c' );
        $automaton->removeEdge( 'a', 'b' );

        $this->assertEquals( array( 'a', 'b', 'c' ), $automaton->getNodes() );
        $this->assertEquals( array( 'c' ), $automaton->getOutgoing( 'a' ) );
        $this->assertEquals( array( 'a' ), $automaton->getIncoming( 'c' ) );
    }

    public function testRemoveUnknownEdge1()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );

        $this->assertFalse( $automaton->removeEdge( 'a', 'unknown' ) );
    }

    public function testRemoveUnknownEdge2()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );

        $this->assertFalse( $automaton->removeEdge( 'unknown', 'b' ) );
    }

    public function testTransitiveClosure1()
    {
        $automaton = $this->getAutomaton();
        $automaton->addNode( 'a' );

        $this->assertEquals(
            array( 'a' ),
            $automaton->transitiveClosure( 'a' )
        );
    }

    public function testTransitiveClosure2()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );

        $this->assertEquals(
            array( 'a', 'b' ),
            $automaton->transitiveClosure( 'a' )
        );
    }

    public function testTransitiveClosure3()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'a', 'c' );
        $automaton->addEdge( 'd', 'b' );

        $this->assertEquals(
            array( 'a', 'b', 'c' ),
            $automaton->transitiveClosure( 'a' )
        );
    }

    public function testTransitiveClosure4()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'b', 'c' );
        $automaton->addEdge( 'c', 'd' );

        $this->assertEquals(
            array( 'a', 'b', 'c', 'd' ),
            $automaton->transitiveClosure( 'a' )
        );
    }

    public function testGetLeaves()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );

        $this->assertEquals(
            array( 'b' ),
            $automaton->getLeaves()
        );
    }

    public function testGetLeaves2()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'c', 'd' );

        $this->assertEquals(
            array( 'b', 'd' ),
            $automaton->getLeaves()
        );
    }

    public function testGetLeaves3()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'b', 'c' );
        $automaton->addEdge( 'c', 'd' );

        $this->assertEquals(
            array( 'd' ),
            $automaton->getLeaves()
        );
    }

    public function testTopologicalSorting()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'b', 'c' );
        $automaton->addEdge( 'c', 'd' );

        $this->assertEquals(
            array( 'a', 'b', 'c', 'd' ),
            $automaton->getTopologicallySortedNodeList()
        );
    }

    public function testTopologicalSorting2()
    {
        $automaton = $this->getAutomaton();
        $automaton->addEdge( 'a', 'b' );
        $automaton->addEdge( 'c', 'd' );

        $this->assertEquals(
            array( 'a', 'b', 'c', 'd' ),
            $automaton->getTopologicallySortedNodeList()
        );
    }
}

