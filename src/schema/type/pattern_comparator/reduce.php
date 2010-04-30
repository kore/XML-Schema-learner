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
    protected $epsilon;

    /**
     * Conxtruct from epsilon
     *
     * Epsilon defines the maximum distance between the two compared 
     * automatons.
     * 
     * @param float $epsilon 
     * @return void
     */
    public function __construct( $epsilon = .25 )
    {
        $this->epsilon = $epsilon;
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
        return ( $this->getDistance( $a, $b ) + $this->getDistance( $b, $a ) ) <= $this->epsilon;
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
                if ( !in_array( $src, $bNodes ) ||
                     !in_array( $dst, $b->automaton->getOutgoing( $src ) ) )
                {
                    $missingVertices += $support;
                }
            }
        }

        return $missingVertices / $vertices;
    }
}

