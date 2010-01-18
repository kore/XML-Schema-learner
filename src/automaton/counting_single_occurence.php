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
 * Extends the single occurence automaton, by additionally counting the number 
 * of occurences of the tokens in each input string
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slCountingSingleOccurenceAutomaton extends slWeightedSingleOccurenceAutomaton
{
    /**
     * Number of occurences for each token
     * 
     * @var array
     */
    protected $occurences = array();

    /**
     * Learn token array into automaton
     * 
     * @param array $tokens 
     * @return void
     */
    public function learn( array $tokens )
    {
        parent::learn( $tokens );

        $signature = $this->getTokenSignature( $tokens );

        $this->occurences[$signature] = $this->mergeCounts(
            isset( $this->occurences[$signature] ) ? $this->occurences[$signature] : array(),
            array_count_values( $tokens )
        );
    }

    /**
     * Get signature from token array
     *
     * Returns a signature for the given token array, which is based on the 
     * occuring tokens
     * 
     * @param array $tokens 
     * @return string
     */
    protected function getTokenSignature( array $tokens )
    {
        $tokens = array_unique( $tokens );
        sort( $tokens );
        return implode( '|', $tokens );
    }

    /**
     * Merge counts of read token sequences into boundaries array
     *
     * Merge the counts of the currently read token sequence into an array, 
     * which contains the minimum and maximum occurence numbers of the tokens 
     * in the array.
     * 
     * @param array $occurences 
     * @param array $counts 
     * @return array
     */
    protected function mergeCounts( array $occurences, array $counts )
    {
        foreach ( $counts as $token => $number )
        {
            if ( !isset( $occurences[$token] ) )
            {
                $occurences[$token] = array(
                    'min' => $number,
                    'max' => $number,
                );
            }
            else
            {
                $occurences[$token] = array(
                    'min' => min( $number, $occurences[$token]['min'] ),
                    'max' => max( $number, $occurences[$token]['max'] ),
                );
            }
        }

        return $occurences;
    }

    /**
     * Get number of token occurences
     *
     * Returns the minimum and maximum number of occurences of the given tokens
     * in each read word.
     *
     * The results will be merged into a single array, which contains the 
     * minimum and maximum occurences of any token in the input array. The 
     * return value looks like:
     *
     * <code>
     *  array(
     *      'min' => $number,
     *      'max' => $number,
     *  )
     * </code>
     *
     * @param string $token 
     * @return array
     */
    public function getOccurences( array $tokens )
    {
        $return = array(
            'min' => PHP_INT_MAX,
            'max' => 0,
        );

        foreach ( $this->occurences as $occurences )
        {
            foreach ( $tokens as $token )
            {
                if ( !isset( $occurences[$token] ) )
                {
                    $return = array(
                        'min' => min( $return['min'], 0 ),
                        'max' => max( $return['max'], 0 ),
                    );
                }
                else
                {
                    $return = array(
                        'min' => min( $return['min'], $occurences[$token]['min'] ),
                        'max' => max( $return['max'], $occurences[$token]['max'] ),
                    );
                }
            }
        }

        return $return;
    }
}

