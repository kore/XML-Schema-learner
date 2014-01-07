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
 * Hidden Markov Model
 *
 * Class representing a Hidden Markov Model.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slHiddenMarkovModel implements Countable
{
    /**
     * Array with node labels in the HMM
     * 
     * @var array
     */
    protected $labels = array();

    /**
     * Array with the transistion probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $start = array();

    /**
     * Array with the transistion probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $transistion = array();

    /**
     * Array with the emission probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $emission = array();

    /**
     * Construct Hidden Markov Model froms set of labels
     * 
     * @param int $states 
     * @param array $labels 
     * @return void
     */
    public function __construct( $states, array $labels )
    {
        $this->labels      = array_values( $labels );
        $this->start       = array_fill( 0, $states, 1 / $states );
        $this->transistion = array_fill( 0, $states, array_fill( 0, $states, 1 / $states ) );
        $this->emission    = array_fill( 0, $states, array_fill( 0, count( $labels ), 1 / count( $labels ) ) );
    }

    /**
     * Get Transition probability from item $x to item $y
     * 
     * @param int $x 
     * @param int $y 
     * @return float
     */
    public function getTransition( $x, $y )
    {
        if ( !isset( $this->transistion[$x] ) ||
             !isset( $this->transistion[$x][$y] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->transistion[$x][$y];
    }

    /**
     * Set Transition probability from item $x to item $y
     * 
     * @param int $x 
     * @param int $y 
     * @param float $p
     * @return void
     */
    public function setTransition( $x, $y, $v )
    {
        if ( !isset( $this->transistion[$x] ) ||
             !isset( $this->transistion[$x][$y] ) )
        {
            throw new OutOfBoundsException();
        }

        $this->transistion[$x][$y] = (float) $v; 
    }

    /**
     * Get Emission probability from item $x to item $y
     * 
     * @param int $x 
     * @param int $y 
     * @return float
     */
    public function getEmission( $x, $y )
    {
        if ( !isset( $this->emission[$x] ) ||
             !isset( $this->emission[$x][$y] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->emission[$x][$y];
    }

    /**
     * Set Emission probability from item $x to item $y
     * 
     * @param int $x 
     * @param int $y 
     * @param float $p
     * @return void
     */
    public function setEmission( $x, $y, $v )
    {
        if ( !isset( $this->emission[$x] ) ||
             !isset( $this->emission[$x][$y] ) )
        {
            throw new OutOfBoundsException();
        }

        $this->emission[$x][$y] = (float) $v; 
    }

    /**
     * Get Start probability for item $x
     * 
     * @param int $x 
     * @return float
     */
    public function getStart( $x )
    {
        if ( !isset( $this->start[$x] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->start[$x];
    }

    /**
     * Set Start probability for item $x
     * 
     * @param int $x 
     * @param float $p
     * @return void
     */
    public function setStart( $x, $v )
    {
        if ( !isset( $this->start[$x] ) )
        {
            throw new OutOfBoundsException();
        }

        $this->start[$x] = (float) $v; 
    }

    /**
     * Map labels to their indexes
     * 
     * @param array $labels 
     * @return array
     */
    public function mapLabels( array $labels )
    {
        $indexes = array();
        foreach ( $labels as $label )
        {
            if ( ( $key = array_search( $label, $this->labels ) ) === false )
            {
                throw new OutOfBoundsException();
            }

            $indexes[] = $key;
        }

        return $indexes;
    }

    /**
     * Get label of item
     * 
     * @param int $x 
     * @return mixed
     */
    public function getLabel( $x )
    {
        if ( !isset( $this->labels[$x] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->labels[$x];
    }

    /**
     * Get number of labels, aka, the dimension of the emissions of the HMM
     * 
     * @return int
     */
    public function countLabels()
    {
        return count( $this->labels );
    }

    /**
     * Get number of states, aka, the internal dimension of the HMM
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->transistion );
    }

    /**
     * Get array of random values
     *
     * Returns an array of the specified size conatining random values, which 
     * sum up to 1.
     * 
     * @param int $size 
     * @return array
     */
    protected function getRandomArray( $size )
    {
        $return = array();
        $left   = 1;
        for ( $i = 0; $i < $size; ++$i )
        {
            if ( $i === ( $size - 1 ) )
            {
                $return[] = $left;
                break;
            }

            $return[] = $v = mt_rand( 0, $left * 10000 ) / 10000;
            $left    -= $v;
        }

        return $return;
    }

    /**
     * Randomize HMM
     *
     * Create random tansistion probabilities for the HMM.
     * 
     * @return void
     */
    public function randomize()
    {
        $states = count( $this->transistion );
        for ( $i = 0; $i < $states; ++$i )
        {
            $this->transistion[$i] = $this->getRandomArray( $states );
            $this->emission[$i]    = $this->getRandomArray( count( $this->labels ) );
        }

        $this->start = $this->getRandomArray( $states );
    }
}

