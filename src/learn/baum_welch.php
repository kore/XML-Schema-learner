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
 * Implementation of the Baum-Welch algorithm for training of Hidden 
 * Markov Models, based on a set of sequences.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slBaumWelchTrainer
{
    /**
     * Train the given sequence multiple times
     *
     * Train the given Hidden Markov Model n times with the specified sequence.
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param array $sequence 
     * @param int $steps 
     * @return void
     */
    public function trainCycle( slHiddenMarkovModel $hmm, array $sequence, $steps )
    {
        for ( $i = 0; $i < $steps; ++$i )
        {
            $this->train( $hmm, $sequence );
        }
    }

    /**
     * Train the HMM with the given input sequence
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param array $sequence 
     * @return void
     */
    public function train( slHiddenMarkovModel $hmm, array $sequence )
    {
        $sequence       = $hmm->mapLabels( $sequence );
        $sequenceLength = count( $sequence );
        $oldHmm         = clone $hmm;
        $states         = count( $hmm );
        $labels         = $hmm->countLabels();

        // Calculate forward and backwards variables, first
        $forward  = $this->calcForward( $oldHmm, $sequence );
        $backward = $this->calcBackwards( $oldHmm, $sequence );

        // Reevaluate the start probabilities
        for ( $i = 0; $i < $states; ++$i )
        {
            $hmm->setStart( $i, $this->calcGamma( $oldHmm, $i, 0, $sequence, $forward, $backward ) );
        }

        // Reevaluate the transistion probabilities
        for ( $i = 0; $i < $states; ++$i )
        {
            for ( $j = 0; $j < $states; ++$j )
            {
                $numerator   = 0;
                $denominator = 0;

                for ( $t = 0; $t <= $sequenceLength - 1; ++$t )
                {
                    $numerator   += $this->calcP( $oldHmm, $t, $i, $j, $sequence, $forward, $backward );
                    $denominator += $this->calcGamma( $oldHmm, $i, $t, $sequence, $forward, $backward );
                }

                $hmm->setTransition( $i, $j, $this->divide( $numerator, $denominator ) );
            }
        }

        // Reevaluate the emission probabilities
        for ( $i = 0; $i < $states; ++$i )
        {
            for ( $k = 0; $k < $labels; ++$k )
            {
                for ( $t = 0; $t <= $sequenceLength - 1; ++$t )
                {
                    $gamma       = $this->calcGamma( $oldHmm, $i, $t, $sequence, $forward, $backward );
                    $numerator   += $gamma * ( $k == $sequence[$t] ? 1 : 0 );
                    $denominator += $gamma;
                }
                $hmm->setEmission( $i, $k, $this->divide( $numerator, $denominator ) );
            }
        }
    }

    /**
     * Calculates the forward variables
     *
     * Calculates the forward variables for the given sequence depending on the 
     * given HMM.
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param array $sequence 
     * @return array
     */
    public function calcForward( slHiddenMarkovModel $hmm, array $sequence )
    {
        $sequenceLength = count( $sequence );
        $states         = count( $hmm );
        $forward        = array();

        // The basic case
        for ( $i = 0; $i < $states; ++$i )
        {
            $forward[$i][0] = $hmm->getStart( $i ) * $hmm->getEmission( $i, $sequence[0] );
        }

        // Structural induction
        for ( $t = 0; $t <= $sequenceLength - 2; ++$t )
        {
            for ( $j = 0; $j < $states; ++$j )
            {
                $forward[$j][$t + 1] = 0;
                for ( $i = 0; $i < $states; ++$i )
                {
                    $forward[$j][$t + 1] += $forward[$i][$t] * $hmm->getTransition( $i, $j );
                }
                $forward[$j][$t + 1] *= $hmm->getEmission( $j, $sequence[$t + 1] );
            }
        }

        return $forward;
    }

    /**
     * Calculates the forward variables
     *
     * Calculates the forward variables for the given sequence depending on the 
     * given HMM.
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param array $sequence 
     * @return array
     */
    public function calcBackwards( slHiddenMarkovModel $hmm, array $sequence )
    {
        $sequenceLength = count( $sequence );
        $states         = count( $hmm );
        $forward        = array();

        // $sequenceLengthhe basic case
        for ( $i = 0; $i < $states; ++$i )
        {
            $backward[$i][$sequenceLength-1] = 1;
        }

        // Structural induction
        for ( $t = $sequenceLength - 2; $t >= 0; --$t )
        {
            for ( $i = 0; $i < $states; ++$i )
            {
                $backward[$i][$t] = 0;
                for ( $j = 0; $j < $states; ++$j )
                {
                    $backward[$i][$t] += $backward[$j][$t + 1] *
                        $hmm->getTransition( $i, $j ) *
                        $hmm->getEmission( $j, $sequence[$t + 1] );
                }
            }
        }

        return $backward;
    }

    /**
     * Calculates the probability: P(X_t = s_i, X_t+1 = s_j | O, m)
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param int $t 
     * @param int $i 
     * @param int $j 
     * @param array $sequence 
     * @param array $forward 
     * @param array $backward 
     * @return float
     */
    public function calcP( slHiddenMarkovModel $hmm, $t, $i, $j, array $sequence, array $forward, array $backward )
    {
        $sequenceLength = count( $sequence );
        $states         = count( $hmm );

        if ( $t === ( $sequenceLength - 1 ) )
        {
            $numerator = $forward[$i][$t] * $hmm->getTransition( $i, $j );
        }
        else
        {
            $numerator = $forward[$i][$t] * $hmm->getTransition( $i, $j ) *
                $backward[$j][$t + 1] * $hmm->getEmission( $j, $sequence[$t + 1] );
        }

        $denominator = 0;
        for ( $k = 0; $k < $states; ++$k )
        {
            $denominator += $forward[$k][$t] * $backward[$k][$t];
        }

        return $this->divide( $numerator, $denominator );
    }

    /**
     * Calculates gamma( i, t )
     * 
     * @param slHiddenMarkovModel $hmm 
     * @param mixed $i 
     * @param mixed $t 
     * @param array $sequence 
     * @param array $forward 
     * @param array $backward 
     * @return void
     */
    public function calcGamma( slHiddenMarkovModel $hmm, $i, $t, array $sequence, array $forward, array $backward )
    {
        $states      = count( $hmm );
        $numerator   = $forward[$i][$t] * $backward[$i][$t];
        $denominator = 0;
        for ( $j = 0; $j < $states; ++$j )
        {
            $denominator += $forward[$j][$t] * $backward[$j][$t];
        }
        return $this->divide( $numerator, $denominator );
    }

    /**
     * Divide two floats
     *
     * Divide two floats, while 0 / 0 = 0.
     * 
     * @param float $n 
     * @param float $d 
     * @return float
     */
    public function divide( $n, $d )
    {
        return abs( $n < .00001 ) ? .0 : $n / $d;
    }
}

