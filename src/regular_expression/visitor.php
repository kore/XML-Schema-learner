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
    public function visit( $regularExpression )
    {
        if ( !is_object( $regularExpression ) )
        {
            return $this->visitElement( $regularExpression );
        }

        switch ( get_class( $regularExpression ) )
        {
            case 'slRegularExpressionChoice':
                return $this->visitChoice( $regularExpression );
            case 'slRegularExpressionSequence':
                return $this->visitSequence( $regularExpression );
            case 'slRegularExpressionOptional':
                return $this->visitOptional( $regularExpression );
            case 'slRegularExpressionRepeated':
                return $this->visitRepeated( $regularExpression );
            default:
                throw new RuntimeException( 'Unknown class: ' . get_class( $regularExpression ) );
        }
    }

    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param string $element 
     * @return mixed
     */
    abstract protected function visitElement( $element );

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
}

