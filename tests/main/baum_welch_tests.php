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
class slMainBaumWelchTests extends PHPUnit_Framework_TestCase
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
     * Test example train
     *
     * As documented here:
     * http://www.indiana.edu/~iulg/moss/hmmcalculations.pdf
     * 
     * @return void
     */
    public function testSimpleTrain()
    {
        $hmm = new slHiddenMarkovModel( 2, array( 'A', 'B' ) );

        // Initialize the model
        $hmm->setStart( 0, .85 );
        $hmm->setStart( 1, .15 );

        $hmm->setTransition( 0, 0, .3 );
        $hmm->setTransition( 0, 1, .7 );
        $hmm->setTransition( 1, 0, .1 );
        $hmm->setTransition( 1, 1, .9 );

        $hmm->setEmission( 0, 0, .4 );
        $hmm->setEmission( 0, 1, .6 );
        $hmm->setEmission( 1, 0, .5 );
        $hmm->setEmission( 1, 1, .5 );

        $trainer = new slBaumWelchTrainer();
        $trainer->trainCycle( $hmm, array( 'A', 'B', 'B', 'A' ), 1 );
        $trainer->trainCycle( $hmm, array( 'B', 'A', 'B' ), 1 );
    }
}

