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
class slMainSchemaTests extends PHPUnit_Framework_TestCase
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

    public function testDtdSchema()
    {
        $dtd = new slDtdSchema();
        $dtd->learnFile( __DIR__ . '/data/simple.xml' );

        $expressions = array();
        foreach ( $dtd->getTypes() as $element )
        {
            $expressions[$element->type->name] = $element->type->regularExpression;
        }

        $this->assertEquals(
            array(
                'alpha' => new slRegularExpressionEmpty(),
                'beta' => new slRegularExpressionEmpty(),
                'root' => new slRegularExpressionSequence( 
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'alpha' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'beta', 'beta' ) )
                ),
            ),
            $expressions
        );
    }

    public function testDtdSchemaLearnMultiple()
    {
        $dtd = new slDtdSchema();
        $dtd->learnFile( __DIR__ . '/data/simple.xml' );
        $dtd->learnFile( __DIR__ . '/data/simple_2.xml' );

        $expressions = array();
        foreach ( $dtd->getTypes() as $element )
        {
            $expressions[$element->type->name] = $element->type->regularExpression;
        }
        ksort( $expressions );

        $this->assertEquals(
            array(
                'alpha' => new slRegularExpressionEmpty(),
                'beta' => new slRegularExpressionEmpty(),
                'optional' => new slRegularExpressionEmpty(),
                'root' => new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'alpha' ) ),
                    new slRegularExpressionOptional(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'optional', 'optional' ) )
                    ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'beta', 'beta' ) )
                ),
            ),
            $expressions
        );
    }

    public function testXSDSchemaTypeMerging()
    {
        $xsd = new slXsdSchema();
        $xsd->setTypeInferencer( new slFullPathTypeInferencer() );
        $xsd->setTypeMerger(
            new slConfigurableTypeMerger(
                new slSchemaTypeEqualPatternComparator(),
                new slSchemaTypeStrictAttributeComparator()
            )
        );
        $xsd->learnFile( __DIR__ . '/data/type_merging.xml' );

        $expressions = array();
        foreach ( $xsd->getTypes() as $element )
        {
            $expressions[$element->type->name] = $element->type->regularExpression;
        }
        ksort( $expressions );

        $this->assertEquals(
            array(
                'root' => new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'beta', 'root/beta' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'gamma', 'root/gamma' ) )
                ),
                'root/alpha' => new slRegularExpressionEmpty(),
                'root/beta' => new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'gamma', 'root/beta/gamma' ) )
                ),
                'root/beta/gamma' => new slRegularExpressionElement( new slSchemaAutomatonNode( 'delta', 'root/alpha' ) ),
                'root/gamma' => new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
            ),
            $expressions
        );
    }

    public function testXSDSchemaMagicTypeMerging()
    {
        $xsd = new slXsdSchema();
        $xsd->setTypeInferencer( new slFullPathTypeInferencer() );
        $xsd->setTypeMerger(
            new slConfigurableTypeMerger(
                new slSchemaTypeSubsumingPatternComparator(),
                new slSchemaTypeEqualAttributeComparator()
            )
        );
        $xsd->learnFile( __DIR__ . '/data/type_merging.xml' );

        $expressions = array();
        foreach ( $xsd->getTypes() as $element )
        {
            $expressions[$element->type->name] = $element->type->regularExpression;
        }
        ksort( $expressions );

        $this->assertEquals(
            array(
                'root' => new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'beta', 'root/beta' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'gamma', 'root/gamma' ) )
                ),
                'root/alpha' => new slRegularExpressionElement( new slSchemaAutomatonNode( 'delta', 'root/alpha' ) ),
                'root/beta' => new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'gamma', 'root/alpha' ) )
                ),
                'root/gamma' => new slRegularExpressionElement( new slSchemaAutomatonNode( 'alpha', 'root/alpha' ) ),
            ),
            $expressions
        );
    }

    public function testDtdSchemaRootElements()
    {
        $dtd = new slDtdSchema();
        $dtd->learnFile( __DIR__ . '/data/simple.xml' );
        $dtd->learnFile( __DIR__ . '/data/simple_2.xml' );

        $this->assertEquals(
            array(
                'root' => 'root',
            ),
            $dtd->getRootElements()
        );
    }

    public function testDtdSchemaMultipleRootElements()
    {
        $dtd = new slDtdSchema();
        $dtd->learnFile( __DIR__ . '/data/simple.xml' );
        $dtd->learnFile( __DIR__ . '/data/simple_2.xml' );
        $dtd->learnFile( __DIR__ . '/data/simple_3.xml' );

        $this->assertEquals(
            array(
                'root' => 'root',
                'xml'  => 'xml',
            ),
            $dtd->getRootElements()
        );
    }
}

