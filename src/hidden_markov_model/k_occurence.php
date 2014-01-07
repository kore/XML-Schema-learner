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
 * k-occurence Hidden Markov Model
 *
 * Class offering a constructor for a k-occurence HMM.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slKOccurenceHiddenMarkovModel extends slHiddenMarkovModel
{
    /**
     * Create a k-Occurence HMM
     *
     * Create a Hidden Markov Model, which has exactly k states for each label, 
     * where the emission probability for each of theses states is 1 for the 
     * respective label.
     * 
     * @param array $labels 
     * @param int $k 
     * @param bool $randomize
     * @return slHiddenMarkovModel
     */
    public static function create( array $labels, $k, $randomize = true )
    {
        $states = count( $labels ) * $k;
        $hmm = new static( $states, $labels );

        // Randomize model, if requested
        if ( $randomize )
        {
            $hmm->randomize();
        }

        // Set fixed emission probabilities
        for ( $i = 0; $i < $states; ++$i )
        {
            for ( $j = 0; $j < count( $labels ); ++$j )
            {
                $hmm->setEmission( $i, $j, (int) ( floor( $i / $k ) == $j ) );
            }
        }

        return $hmm;
    }
}

