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
 * Regular expression visitor base class.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slRegularExpressionVisitor
{
    /**
     * Main visit function
     *
     * Visit function to process a regular expression, or a sub expression. 
     * Recursively dispatches to the concrete visit functions, which should be 
     * implemented by concrete visitors.
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param mixed $regularExpression 
     * @return mixed
     */
    public function visit( slRegularExpression $regularExpression )
    {
        switch ( get_class( $regularExpression ) )
        {
            case 'slRegularExpressionEmpty':
                return $this->visitEmpty( $regularExpression );
            case 'slRegularExpressionElement':
                return $this->visitElement( $regularExpression );
            case 'slRegularExpressionChoice':
                return $this->visitChoice( $regularExpression );
            case 'slRegularExpressionSequence':
                return $this->visitSequence( $regularExpression );
            case 'slRegularExpressionAll':
                return $this->visitAll( $regularExpression );
            case 'slRegularExpressionOptional':
                return $this->visitOptional( $regularExpression );
            case 'slRegularExpressionRepeated':
                return $this->visitRepeated( $regularExpression );
            case 'slRegularExpressionRepeatedAtLeastOnce':
                return $this->visitRepeatedAtLeastOnce( $regularExpression );
        }
    }

    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionEmpty $element 
     * @return mixed
     */
    abstract protected function visitEmpty( slRegularExpressionEmpty $element );

    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionElement $element 
     * @return mixed
     */
    abstract protected function visitElement( slRegularExpressionElement $element );

    /**
     * Visit choice sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionChoice $regularExpression 
     * @return mixed
     */
    abstract protected function visitChoice( slRegularExpressionChoice $regularExpression );

    /**
     * Visit sequence sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    abstract protected function visitSequence( slRegularExpressionSequence $regularExpression );

    /**
     * Visit all sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    abstract protected function visitAll( slRegularExpressionAll $regularExpression );

    /**
     * Visit optional sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionOptional $regularExpression 
     * @return mixed
     */
    abstract protected function visitOptional( slRegularExpressionOptional $regularExpression );

    /**
     * Visit repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeated $regularExpression 
     * @return mixed
     */
    abstract protected function visitRepeated( slRegularExpressionRepeated $regularExpression );

    /**
     * Visit at least once repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeatedAtLeastOnce $regularExpression 
     * @return mixed
     */
    abstract protected function visitRepeatedAtLeastOnce( slRegularExpressionRepeatedAtLeastOnce $regularExpression );
}

