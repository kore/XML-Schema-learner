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
 * Single occurence automaton class, which implements and learns automatons 
 * from strings, where each string element only occurs once, and thus has a 
 * direct representation in the automaton.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSingleOccurenceAutomaton extends slAutomaton
{
    /**
     * Learn token array into automaton
     * 
     * @param array $tokens 
     * @return void
     */
    public function learn( array $tokens )
    {
        switch ( count( $tokens ) )
        {
            case 0:
                return;

            case 1:
                $this->nodes[(string) reset( $tokens )] = reset( $tokens );
                return;
        }

        $first = array_shift( $tokens );
        while ( $next = array_shift( $tokens ) )
        {
            $this->addEdge( $first, $next );
            $first = $next;
        }
    }
}

