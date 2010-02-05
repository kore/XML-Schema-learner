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
     * Construct Hidden Markov Model froms et of labels
     * 
     * @param array $labels 
     * @param array $start 
     * @param array $transistion 
     * @return void
     */
    public function __construct( array $labels, array $start = null, array $transistion = null )
    {
        $this->labels = array_values( $labels );

        $count = count( $labels );
        $this->start  = $start === null ? array_fill( 0, $count, 1 / $count ) : $start;

        $this->transistion = $transistion;
        if ( $this->transistion === null )
        {
            $this->transistion = array_fill( 0, $count, array_fill( 0, $count, 1 / $count ) );
        }
    }

    /**
     * Get Transistion probability from item $x to item $y
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
     * Get number of lables, aka, the dimension of the HMM
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->labels );
    }

    /**
     * Randomize HMM
     *
     * Create random tansistion probabilities for the HMM.
     * 
     * @return void
     */
    public function randomize( $count = null )
    {
        $labels     = count( $this->labels );
        $iterations = $count === null ? $labels : $count;
        for ( $n = 0; $n < $iterations; ++$n )
        {
            for ( $m = 0; $m < $iterations; ++$m )
            {
                // Poke
                $x = mt_rand( 1, $labels - 2 );
                $y = mt_rand( 1, $labels - 2 );

                $diff = mt_rand(
                    $min = max(
                        -$this->transistion[$x][$y],
                        -( 1 - $this->transistion[$x - 1][$y] ) * 2,
                        -( 1 - $this->transistion[$x + 1][$y] ) * 2,
                        -( 1 - $this->transistion[$x][$y - 1] ) * 2,
                        -( 1 - $this->transistion[$x][$y + 1] ) * 2,
                        -$this->transistion[$x - 1][$y - 1] * 4,
                        -$this->transistion[$x - 1][$y + 1] * 4,
                        -$this->transistion[$x + 1][$y - 1] * 4,
                        -$this->transistion[$x + 1][$y + 1] * 4
                    ) * 1000,
                    $max = min(
                        1 - $this->transistion[$x][$y],
                        $this->transistion[$x - 1][$y] * 2,
                        $this->transistion[$x + 1][$y] * 2,
                        $this->transistion[$x][$y - 1] * 2,
                        $this->transistion[$x][$y + 1] * 2,
                        ( 1 - $this->transistion[$x - 1][$y - 1] ) * 4,
                        ( 1 - $this->transistion[$x - 1][$y + 1] ) * 4,
                        ( 1 - $this->transistion[$x + 1][$y - 1] ) * 4,
                        ( 1 - $this->transistion[$x + 1][$y + 1] ) * 4
                    ) * 1000
                );
                $diff /= 1000;
                
                $this->transistion[$x][$y] += $diff;
                $this->transistion[$x - 1][$y] -= $diff / 2;
                $this->transistion[$x + 1][$y] -= $diff / 2;
                $this->transistion[$x][$y - 1] -= $diff / 2;
                $this->transistion[$x][$y + 1] -= $diff / 2;
                $this->transistion[$x - 1][$y - 1] += $diff / 4;
                $this->transistion[$x - 1][$y + 1] += $diff / 4;
                $this->transistion[$x + 1][$y - 1] += $diff / 4;
                $this->transistion[$x + 1][$y + 1] += $diff / 4;
            }

            for ( $m = 0; $m < $iterations; ++$m )
            {
                // Switch column / rows
                if ( $m % 2 )
                {
                    $x1 = mt_rand( 0, $labels - 1 );
                    $x2 = mt_rand( 0, $labels - 1 );

                    $tmp = $this->transistion[$x1];
                    $this->transistion[$x1] = $this->transistion[$x2];
                    $this->transistion[$x2] = $tmp;
                }
                else
                {
                    $y1 = mt_rand( 0, $labels - 1 );
                    $y2 = mt_rand( 0, $labels - 1 );

                    for ( $x = 0; $x < $labels; ++$x )
                    {
                        $tmp = $this->transistion[$x][$y1];
                        $this->transistion[$x][$y1] = $this->transistion[$x][$y2];
                        $this->transistion[$x][$y2] = $tmp;
                    }
                }
            }
        }

        $this->start = $this->transistion[mt_rand( 0, $labels - 1 )];
    }
}

