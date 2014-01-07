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
class slRegularExpressionStringVisitor extends slRegularExpressionVisitor
{
    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionEmpty $element 
     * @return mixed
     */
    protected function visitEmpty( slRegularExpressionEmpty $element )
    {
        return '';
    }

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
        return (string) $element->getContent();
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
            implode( ' + ', array_map( array( $this, 'visit' ), $regularExpression->getChildren() ) ) .
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
            implode( ' ', array_map( array( $this, 'visit' ), $regularExpression->getChildren() ) ) .
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
        return '( ' .
            implode( ' & ', array_map( array( $this, 'visit' ), $regularExpression->getChildren() ) ) .
        ' )';
    }

    /**
     * Visit optional sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionOptional $regularExpression 
     * @return mixed
     */
    protected function visitOptional( slRegularExpressionOptional $regularExpression )
    {
        return $this->visit( $regularExpression->getChild() ) . '?';
    }

    /**
     * Visit repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeated $regularExpression 
     * @return mixed
     */
    protected function visitRepeated( slRegularExpressionRepeated $regularExpression )
    {
        return $this->visit( $regularExpression->getChild() ) . '*';
    }

    /**
     * Visit at least once repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeatedAtLeastOnce $regularExpression 
     * @return mixed
     */
    protected function visitRepeatedAtLeastOnce( slRegularExpressionRepeatedAtLeastOnce $regularExpression )
    {
        return $this->visit( $regularExpression->getChild() ) . '+';
    }
}

