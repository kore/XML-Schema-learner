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
class slVisitorAutomatonDotTests extends PHPUnit_Framework_TestCase
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

    public static function getAutomatons()
    {
        return array(
            array(
                array(
                    array( 'a' ),
                ),
                array(),
                'single_node_plain'
            ),
            array(
                array(
                    array( 'a' ),
                    array( 'b' ),
                ),
                array(),
                'two_nodes_seperated'
            ),
            array(
                array(
                    array( 'a', 'b' ),
                ),
                array(),
                'two_nodes_connected'
            ),
            array(
                array(
                    array( 'a', 'b' ),
                    array( 'a', 'c' ),
                ),
                array(),
                'three_nodes_partially_connected'
            ),
            array(
                array(
                    array( 'a', 'b' ),
                    array( 'a', 'c' ),
                ),
                array(
                    'a' => 'Node a',
                    'b' => 'b?'
                ),
                'partially_relabled'
            ),
        );
    }

    /**
     * @dataProvider getAutomatons
     */
    public function testVisitAutomaton( array $sequences, array $labels, $name )
    {
        $visitor = new slAutomatonDotVisitor();
        $automaton = new slSingleOccurenceAutomaton();
        foreach ( $sequences as $sequence )
        {
            $automaton->learn( $sequence );
        }
        $result = $visitor->visit( $automaton, $labels );

        // Read expectation from file, if available
        if ( !is_file( $file = __DIR__ . '/data/' . $name . '.dot' ) )
        {
            $this->MarkTestSkipped( "No comparision file available; Generated result:\n" . $result );
        }

        $this->assertEquals(
            file_get_contents( $file ),
            $result
        );
    }
}

