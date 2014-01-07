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
 * Pattern comparor based on the REDUCE algorithm described in:
 *
 * "Inferring XML Schema Definitions from XML Data"
 * by
 *  - Geert Jan Bex
 *  - Frank Neven
 *  - Stijn Vansummeren
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaTypeReducePatternComparator extends slSchemaTypePatternComparator
{
    /**
     * Epsilon, which describes the maximum distance between two compared 
     * automatons.
     *
     * Should be a value between 0 and 1 (ionclusive).
     * 
     * @var float
     */
    protected $𝛆;

    /**
     * Construct from 𝛆
     *
     * 𝛆 defines the maximum distance between the two compared automatons.
     *
     * 𝛆 ∈ [0, 1]
     * 
     * @param float $𝛆 
     * @return void
     */
    public function __construct( $𝛆 = .25 )
    {
        $this->𝛆 = $𝛆;
    }

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
        return ( $this->getDistance( $a, $b ) + $this->getDistance( $b, $a ) ) <= $this->𝛆;
    }

    /**
     * Calculate distance between two automatons
     *
     * Calculate distance between two automatons as defined in the REDUCE 
     * algorithm specification.
     * 
     * @param slSchemaType $a 
     * @param slSchemaType $b 
     * @return void
     */
    protected function getDistance( slSchemaType $a, slSchemaType $b )
    {
        $vertices        = 0;
        $missingVertices = 0;

        $bNodes = $b->automaton->getNodes();
        foreach ( $a->automaton->getNodes() as $src )
        {
            foreach ( $a->automaton->getOutgoing( $src ) as $dst )
            {
                $support   = $a->automaton->getEdgeWeight( $src, $dst );
                $vertices += $support;
                if ( !in_array( $src, $bNodes, true ) ||
                     !in_array( $dst, $b->automaton->getOutgoing( $src ), true ) )
                {
                    $missingVertices += $support;
                }
            }
        }

        return $vertices === 0 ? 0 : $missingVertices / $vertices;
    }
}

