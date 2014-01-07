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
 * Test class
 */
class slVisitorRegularExpressionXmlSchemaTests extends PHPUnit_Framework_TestCase
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

    protected function getVisitor()
    {
        $schemaVisitor = new slSchemaXmlSchemaVisitor();
        $schemaVisitor->setTypes( array(
            '23' => new slSchemaElement( '23', new slSchemaType( '23' ) ),
            'a'  => new slSchemaElement( 'a', new slSchemaType( 'a' ) ),
            'b'  => new slSchemaElement( 'b', new slSchemaType( 'b' ) ),
            'b1' => new slSchemaElement( 'b1', new slSchemaType( 'b1' ) ),
            'b2' => new slSchemaElement( 'b2', new slSchemaType( 'b2' ) ),
            'c'  => new slSchemaElement( 'c', new slSchemaType( 'c' ) ),
        ) );

        return new slRegularExpressionXmlSchemaVisitor(
            $schemaVisitor,
            new DOMDocument()
        );
    }

    public function testVisitEmpty()
    {
        $visitor = $this->getVisitor();
        $this->assertTrue(
            $visitor->visit(
                new slRegularExpressionEmpty()
            ) instanceof DOMComment
        );
    }

    public function testVisitElement()
    {
        $visitor = $this->getVisitor();
        $this->assertSame(
            '<element xmlns="http://www.w3.org/2001/XMLSchema" name="a" type="a"/>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
            ) )->asXml()
        );
    }

    public function testVisitNumericElement()
    {
        $visitor = $this->getVisitor();
        $this->assertSame(
            '<element xmlns="http://www.w3.org/2001/XMLSchema" name="23" type="23"/>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionElement( new slSchemaAutomatonNode( 23, 23 ) )
            ) )->asXml()
        );
    }

    public function testVisitSequence()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><element name="a" type="a"/><element name="b" type="b"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitChoice()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<choice xmlns="http://www.w3.org/2001/XMLSchema"><element name="a" type="a"/><element name="b" type="b"/></choice>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitAll()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<all xmlns="http://www.w3.org/2001/XMLSchema"><element name="a" type="a"/><element name="b" type="b"/></all>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionAll(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) ),
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitOptional()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema" minOccurs="0" maxOccurs="1"><element name="a" type="a"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionOptional(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitRepeated()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema" minOccurs="0" maxOccurs="unbounded"><element name="a" type="a"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionRepeated(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitRepeatedAtLeastOnce()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema" minOccurs="1" maxOccurs="unbounded"><element name="a" type="a"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionRepeatedAtLeastOnce(
                    new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                )
            ) )->asXml()
        );
    }

    public function testVisitStackedSequence()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><sequence><element name="b" type="b"/></sequence></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                    )
                )
            ) )->asXml()
        );
    }

    public function testVisitConcatenationOfDisjunction()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><choice><element name="b1" type="b1"/><element name="b2" type="b2"/></choice></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionChoice(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b1', 'b1' ) ),
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'b2', 'b2' ) )
                    )
                )
            ) )->asXml()
        );
    }

    public function testDisjunctionOfConcatenation()
    {
        $visitor = $this->getVisitor();
        $this->assertEquals(
            '<choice xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><sequence><sequence><element name="b" type="b"/></sequence><sequence><element name="c" type="c"/></sequence></sequence></choice>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( new slSchemaAutomatonNode( 'a', 'a' ) )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( new slSchemaAutomatonNode( 'b', 'b' ) )
                        ),
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( new slSchemaAutomatonNode( 'c', 'c' ) )
                        )
                    )
                )
            ) )->asXml()
        );
    }
}

