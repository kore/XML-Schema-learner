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

    public function testVisitEmpty()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertTrue(
            $visitor->visit(
                new slRegularExpressionEmpty()
            ) instanceof DOMComment
        );
    }

    public function testVisitElement()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertSame(
            '<element xmlns="http://www.w3.org/2001/XMLSchema" name="a" type="a"/>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionElement( 'a' )
            ) )->asXml()
        );
    }

    public function testVisitNumericElement()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertSame(
            '<element xmlns="http://www.w3.org/2001/XMLSchema" name="23" type="23"/>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionElement( 23 )
            ) )->asXml()
        );
    }

    public function testVisitSequence()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><element name="a" type="a"/><element name="b" type="b"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionElement( 'a' ),
                    new slRegularExpressionElement( 'b' )
                )
            ) )->asXml()
        );
    }

    public function testVisitChoice()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<choice xmlns="http://www.w3.org/2001/XMLSchema"><element name="a" type="a"/><element name="b" type="b"/></choice>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionElement( 'a' ),
                    new slRegularExpressionElement( 'b' )
                )
            ) )->asXml()
        );
    }

    public function testVisitOptional()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema" minOccurs="0" maxOccurs="1"><element name="a" type="a"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionOptional(
                    new slRegularExpressionElement( 'a' )
                )
            ) )->asXml()
        );
    }

    public function testVisitRepeated()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema" minOccurs="0" maxOccurs="unbounded"><element name="a" type="a"/></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionRepeated(
                    new slRegularExpressionElement( 'a' )
                )
            ) )->asXml()
        );
    }

    public function testVisitStackedSequence()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><sequence><element name="b" type="b"/></sequence></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( 'a' )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( 'b' )
                    )
                )
            ) )->asXml()
        );
    }

    public function testVisitConcatenationOfDisjunction()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<sequence xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><choice><element name="b1" type="b1"/><element name="b2" type="b2"/></choice></sequence>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionSequence(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( 'a' )
                    ),
                    new slRegularExpressionChoice(
                        new slRegularExpressionElement( 'b1' ),
                        new slRegularExpressionElement( 'b2' )
                    )
                )
            ) )->asXml()
        );
    }

    public function testDisjunctionOfConcatenation()
    {
        $visitor = new slRegularExpressionXmlSchemaVisitor( $doc = new DOMDocument() );
        $this->assertEquals(
            '<choice xmlns="http://www.w3.org/2001/XMLSchema"><sequence><element name="a" type="a"/></sequence><sequence><sequence><element name="b" type="b"/></sequence><sequence><element name="c" type="c"/></sequence></sequence></choice>',
            simplexml_import_dom( $visitor->visit(
                new slRegularExpressionChoice(
                    new slRegularExpressionSequence(
                        new slRegularExpressionElement( 'a' )
                    ),
                    new slRegularExpressionSequence(
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( 'b' )
                        ),
                        new slRegularExpressionSequence(
                            new slRegularExpressionElement( 'c' )
                        )
                    )
                )
            ) )->asXml()
        );
    }
}

