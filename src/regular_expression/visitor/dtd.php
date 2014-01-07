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
 * Regular expression string visitor
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slRegularExpressionDtdVisitor extends slRegularExpressionStringVisitor
{
    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionElement $element 
     * @return mixed
     */
    protected function visitElement( slRegularExpressionElement $element )
    {
        return (string) $element->getContent()->name;
    }

    /**
     * Visit choice sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionChoice $regularExpression 
     * @return mixed
     */
    protected function visitChoice( slRegularExpressionChoice $regularExpression )
    {
        return '( ' .
            implode( ' | ', array_map( array( $this, 'visit' ), $regularExpression->getChildren() ) ) .
        ' )';
    }

    /**
     * Visit sequence sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    protected function visitSequence( slRegularExpressionSequence $regularExpression )
    {
        return '( ' .
            implode( ', ', array_map( array( $this, 'visit' ), $regularExpression->getChildren() ) ) .
        ' )';
    }

    /**
     * Visit all sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    protected function visitAll( slRegularExpressionAll $regularExpression )
    {
        $children = array_map( array( $this, 'visit' ), $regularExpression->getChildren() );

        $ordered = $this->getAllCombinations( $children );
        return '( ( ' . implode( ' ) | ( ', array_map(
            function ( $children )
            {
                return implode( ', ', $children );
            },
            $ordered
        ) ) . ' ) )';
    }

    /**
     * Get all sorting combinations for passed array
     *
     * Returns an array with arrays, where each array contains one of the 
     * possible sorting combinations of the iput array.
     * 
     * @param array $array 
     * @return array
     */
    protected function getAllCombinations( array $array )
    {
        if ( count( $array ) === 1 )
        {
            return array( $array );
        }

        $combinations = array();
        foreach ( $array as $index => $value )
        {
            $passed = $array;
            unset( $passed[$index] );
            foreach ( $this->getAllCombinations( $passed ) as $combination )
            {
                $combinations[] = array_merge( array( $value ), $combination );
            }
        }

        return $combinations;
    }
}

