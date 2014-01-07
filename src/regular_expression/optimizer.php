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
 * Regular expression optimizer.
 *
 * Aggregates and manages the actual optimization implementations, and 
 * dispatches the regular expression AST to them.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slRegularExpressionOptimizer
{
    /**
     * List of optimizers to use.
     * 
     * @var array
     */
    protected $optimizers = array();

    /**
     * Construct optimizer from set of optimizer implementations
     *
     * Construct the optimizer from an optional custom set of optimizer 
     * implementations. If not provided a sensible default set will be used.
     * 
     * @param array $optimizers 
     * @return void
     */
    public function __construct( array $optimizers = null )
    {
        if ( $optimizers !== null )
        {
            $this->optimizers = $optimizers;
            return;
        }

        $this->optimizers = array(
            new slRegularExpressionChoiceOptimizer(),
            new slRegularExpressionSequenceOptimizer(),
            new slRegularExpressionRepetitionOptimizer(),
            new slRegularExpressionSingletonOptimizer(),
            new slRegularExpressionEmptyOptimizer(),
            new slRegularExpressionEmptyChildOptimizer(),
        );
    }

    /**
     * Optimize regular expression
     *
     * Tries to optimize the given regular expression using the set of provided 
     * concrete optimizer implementations.
     * 
     * @param slRegularExpression $regularExpression 
     * @return void
     */
    public function optimize( slRegularExpression &$regularExpression )
    {
        do {
            $optimized = false;
            foreach ( $this->optimizers as $optimizer )
            {
                $optimized |= $optimizer->optimize( $regularExpression );
            }
        } while ( $optimized );
    }
}

