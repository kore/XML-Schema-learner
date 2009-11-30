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
class slCountingSingleOccurenceAutomaton extends slSingleOccurenceAutomaton
{
    /**
     * Number of occurences for each token
     * 
     * @var array
     */
    protected $occurences = array();

    /**
     * Number of already learned token sequences
     * 
     * @var int
     */
    protected $learnedSequences = 0;

    /**
     * Learn token array into automaton
     * 
     * @param array $tokens 
     * @return void
     */
    public function learn( array $tokens )
    {
        parent::learn( $tokens );

        $tokenCounts = array_count_values( $tokens );
        if ( $this->learnedSequences++ === 0 )
        {
            foreach ( $tokenCounts as $token => $count )
            {
                $this->occurences[$token][0] = false;
                $this->occurences[$token][1] = $count === 1;
                $this->occurences[$token][2] = $count > 1;
            }
        }
        else
        {
            // Update numbers for existing tokens
            foreach ( $this->occurences as $token => $occurences )
            {
                if ( !isset( $tokenCounts[$token] ) )
                {
                    $this->occurences[$token][0] = true;
                }
                else
                {
                    $this->occurences[$token][$tokenCounts[$token] > 1 ? 2 : 1] = true;
                }
                unset( $tokenCounts[$token] );
            }

            // Update numbers for newly learned tokens
            foreach ( $tokenCounts as $token => $count )
            {
                $this->occurences[$token][0] = true;
                $this->occurences[$token][1] = $count === 1;
                $this->occurences[$token][2] = $count > 1;
            }
        }
    }

    /**
     * Get number of token occurences
     *
     * Returns how often the token occured in each of the input strings. Retuns 
     * an array, which is structured like:
     *
     * <code>
     *  array(
     *      0 => bool,
     *      1 => bool,
     *      2 => bool,
     *  )
     * </code>
     * 
     * Where the 0-value indicates, whether that did token wasn't available 
     * in at least one input string, the 1-value means that it did occure 
     * exactly once in at least one input string, and the 2-value means that it 
     * occured multiple times in at least one input string.
     *
     * @param string $token 
     * @return array
     */
    public function getOccurences( $token )
    {
        if ( !isset( $this->occurences[$token] ) )
        {
            return array( true, false, false );
        }

        return $this->occurences[$token];
    }
}

