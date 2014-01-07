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
 * Pattern comparator
 *
 * Abstract base class for implementations for comparing the child patterns / 
 * element patterns of an element / type.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaTypeEqualPatternComparator extends slSchemaTypePatternComparator
{
    /**
     * Compare attributes
     *
     * Returns true, if the attributes are the same, by definition of the used 
     * comparision algorithm.
     * 
     * @param slSchemaType $a 
     * @param slSchemaType $b 
     * @return bool
     */
    public function compare( slSchemaType $a, slSchemaType $b )
    {
        if ( !$this->compareNodes( $a, $b ) )
        {
            return false;
        }

        foreach ( $a->automaton->getNodes() as $node )
        {
            if ( ( $a->automaton->getOutgoing( $node ) !== $b->automaton->getOutgoing( $node ) ) ||
                 ( $a->automaton->getIncoming( $node ) !== $b->automaton->getIncoming( $node ) ) )
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Compare automaton nodes
     *
     * Compare if both type automatons refer to the same nodes.
     * 
     * @param slSchemaType $a 
     * @param slSchemaType $b 
     * @return bool
     */
    protected function compareNodes( slSchemaType $a, slSchemaType $b )
    {
        $diff = array_merge(
            array_diff( $a->automaton->getNodes(), $b->automaton->getNodes() ),
            array_diff( $b->automaton->getNodes(), $a->automaton->getNodes() )
        );
        
        return !count( $diff );
    }
}

