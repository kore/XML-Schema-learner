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
 * Test class
 */
class slVisitorSchemaXmlSchemaTests extends PHPUnit_Framework_TestCase
{
    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public static function getSchemaDefinitions()
    {
        return array(
            array(
                array(
                    __DIR__ . '/../main/data/simple.xml',
                ),
                'simple'
            ),
            array(
                array(
                    __DIR__ . '/../main/data/simple.xml',
                    __DIR__ . '/../main/data/simple_2.xml',
                ),
                'simple_2'
            ),
        );
    }

    /**
     * @dataProvider getSchemaDefinitions
     */
    public function testVisitAutomaton( array $xmlFiles, $name )
    {
        $xsd = new slDtdSchema();
        foreach ( $xmlFiles as $file )
        {
            $xsd->learnFile( $file );
        }

        $visitor = new slSchemaXmlSchemaVisitor();
        $result = $visitor->visit( $xsd );

        // Read expectation from file, if available
        if ( !is_file( $file = __DIR__ . '/data/' . $name . '.xsd' ) )
        {
            $this->MarkTestSkipped( "No comparision file ($file) available; Generated result:\n" . $result );
        }

        $this->assertEquals(
            file_get_contents( $file ),
            $result
        );
    }
}

