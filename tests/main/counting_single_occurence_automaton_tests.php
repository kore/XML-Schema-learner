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
class slMainCountingSingleOccurenceAutomatonTests extends slMainWeightedSingleOccurenceAutomatonTests
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
        return new slCountingSingleOccurenceAutomaton();
    }

    public function testCounting()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a' ) );

        $this->assertEquals(
            array(
                'min' => 1,
                'max' => 1,
            ),
            $automaton->getOccurences( array( 'a' ) )
        );
    }

    public function testCounting2()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a', 'a' ) );

        $this->assertEquals(
            array(
                'min' => 2,
                'max' => 2,
            ),
            $automaton->getOccurences( array( 'a' ) )
        );
    }

    public function testCounting3()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b' ) );

        $this->assertEquals(
            array(
                'min' => 0,
                'max' => 1,
            ),
            $automaton->getOccurences( array( 'a' ) )
        );
    }

    public function testCounting4()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b' ) );

        $this->assertEquals(
            array(
                'min' => 0,
                'max' => 1,
            ),
            $automaton->getOccurences( array( 'b' ) )
        );
    }

    public function testCounting5()
    {
        $automaton = $this->getAutomaton();
        $automaton->learn( array( 'a', 'b', 'b' ) );
        $automaton->learn( array( 'a' ) );

        $this->assertEquals(
            array(
                'min' => 0,
                'max' => 2,
            ),
            $automaton->getOccurences( array( 'a', 'b' ) )
        );
    }
}

