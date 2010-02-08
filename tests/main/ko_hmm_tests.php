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
class slMainKOccurenceHiddenMarkovModelTests extends PHPUnit_Framework_TestCase
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
        $hmm = slKOccurenceHiddenMarkovModel::create( array( 'foo', 'bar' ), 3 );

        $this->assertSame( 6, count( $hmm ) );
    }

    public function testEmissionProbabilities()
    {
        $hmm = slKOccurenceHiddenMarkovModel::create( array( 'foo', 'bar' ), 3 );

        $this->assertSame( 1., $hmm->getEmission( 0, 0 ) );
        $this->assertSame( 1., $hmm->getEmission( 1, 0 ) );
        $this->assertSame( 1., $hmm->getEmission( 2, 0 ) );
        $this->assertSame( 0., $hmm->getEmission( 3, 0 ) );
        $this->assertSame( 0., $hmm->getEmission( 4, 0 ) );
        $this->assertSame( 0., $hmm->getEmission( 5, 0 ) );

        $this->assertSame( 0., $hmm->getEmission( 0, 1 ) );
        $this->assertSame( 0., $hmm->getEmission( 1, 1 ) );
        $this->assertSame( 0., $hmm->getEmission( 2, 1 ) );
        $this->assertSame( 1., $hmm->getEmission( 3, 1 ) );
        $this->assertSame( 1., $hmm->getEmission( 4, 1 ) );
        $this->assertSame( 1., $hmm->getEmission( 5, 1 ) );
    }
}

