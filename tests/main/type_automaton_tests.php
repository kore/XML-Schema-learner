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
class slMainTypeAutomatonTests extends slMainAutomatonTests
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
        return new slTypeAutomaton();
    }

    public function testGetAncestorPatterns1()
    {
        $automaton = $this->getAutomaton();

        $automaton->addEdge( '_start', 'type_1', 'a' );
        $automaton->addEdge( '_start', 'type_2', 'b' );
        $automaton->addEdge( 'type_2', 'type_4', 'c' );
        $automaton->addEdge( 'type_2', 'type_3', 'a' );
        $automaton->addEdge( 'type_2', 'type_2', 'd' );
        $automaton->addEdge( 'type_4', 'type_3', 'a' );
        $automaton->addEdge( 'type_4', 'type_2', 'd' );

        $this->assertSame(
            array(
                'type_1' => array(
                    array( '^', 'a' ),
                ),
                'type_3' => array(
                    array( 'b', 'a' ),
                    array( 'd', 'a' ),
                    array( 'c', 'a' ),
                ),
                'type_2' => array(
                    array( 'b' ),
                    array( 'd' ),
                ),
                'type_4' => array(
                    array( 'c' ),
                ),
            ),
            $automaton->getAncestorPatterns()
        );
    }

    public function testGetAncestorPatterns2()
    {
        $automaton = $this->getAutomaton();

        $automaton->addEdge( '_start', 'type_1', 'a' );
        $automaton->addEdge( '_start', 'type_2', 'b' );
        $automaton->addEdge( 'type_1', 'type_3', 'c' );
        $automaton->addEdge( 'type_2', 'type_4', 'c' );
        $automaton->addEdge( 'type_3', 'type_5', 'd' );
        $automaton->addEdge( 'type_4', 'type_6', 'd' );

        $this->assertSame(
            array(
                'type_1' => array(
                    array( 'a' ),
                ),
                'type_2' => array(
                    array( 'b' ),
                ),
                'type_3' => array(
                    array( 'a', 'c' ),
                ),
                'type_4' => array(
                    array( 'b', 'c' ),
                ),
                'type_5' => array(
                    array( 'a', 'c', 'd' ),
                ),
                'type_6' => array(
                    array( 'b', 'c', 'd' ),
                ),
            ),
            $automaton->getAncestorPatterns()
        );
    }

    public function testGetAncestorPatterns3()
    {
        $automaton = $this->getAutomaton();

        $automaton->addEdge( '_start', 'type_1', 'a' );
        $automaton->addEdge( '_start', 'type_2', 'b' );
        $automaton->addEdge( 'type_1', 'type_3', 'c' );
        $automaton->addEdge( 'type_2', 'type_4', 'c' );
        $automaton->addEdge( 'type_3', 'type_5', 'd' );
        $automaton->addEdge( 'type_4', 'type_5', 'd' );

        $this->assertSame(
            array(
                'type_1' => array(
                    array( 'a' ),
                ),
                'type_2' => array(
                    array( 'b' ),
                ),
                'type_3' => array(
                    array( 'a', 'c' ),
                ),
                'type_4' => array(
                    array( 'b', 'c' ),
                ),
                'type_5' => array(
                    array( 'd' ),
                ),
            ),
            $automaton->getAncestorPatterns()
        );
    }
}

