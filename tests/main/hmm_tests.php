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
class slMainHiddenMarkovModelTests extends PHPUnit_Framework_TestCase
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

    public function testInitializationCount()
    {
        $hmm = new slHiddenMarkovModel( 4, array( 1, 2, 3 ) );
        
        $this->assertSame( 4, count( $hmm ) );
    }

    public function testInitializationValues()
    {
        $hmm = new slHiddenMarkovModel( 4, array( 1, 2, 3 ) );
        
        $size = count( $hmm );
        for ( $x = 0; $x < $size; ++$x )
        {
            for ( $y = 0; $y < $size; ++$y )
            {
                $this->assertEquals( .25, $hmm->getTransition( $x, $y ), .001 );
            }
        }
    }

    public function testGetLabels()
    {
        $hmm = new slHiddenMarkovModel( 4, array( 1, 2, 3 ) );
     
        $this->assertSame(
            2,
            $hmm->getLabel( 1 )
        );   
    }

    public function testRandomize()
    {
        $hmm = new slHiddenMarkovModel( 4, array( 1, 2, 3 ) );
        $hmm->randomize();

        $size = count( $hmm );
        for ( $x = 0; $x < $size; ++$x )
        {
            $rowSum = 0;
            for ( $y = 0; $y < $size; ++$y )
            {
                $rowSum += $hmm->getTransition( $x, $y );
            }

            $this->assertEquals( 1., $rowSum, null, .001 );
        }
    }
}

