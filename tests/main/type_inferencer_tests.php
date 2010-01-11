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
class slMainTypeInferencerTests extends PHPUnit_Framework_TestCase
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

    public function testInferenceNameBasedType()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><element/></xml>' );

        $typeInferencer = new slNameBasedTypeInferencer();
        $this->assertEquals( 'element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceNameBasedType2()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><element/></xml>' );

        $typeInferencer = new slNameBasedTypeInferencer();
        $this->assertEquals( 'xml', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'xml' )->item( 0 ) ) );
    }

    public function testInferenceKLocalType0()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slKLocalTypeInferencer( 0 );
        $this->assertEquals( 'element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceKLocalType1()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slKLocalTypeInferencer( 1 );
        $this->assertEquals( 'root/element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceKLocalType2()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slKLocalTypeInferencer( 2 );
        $this->assertEquals( 'xml/root/element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceKLocalType3()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slKLocalTypeInferencer( 3 );
        $this->assertEquals( 'xml/root/element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceFullPathType1()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slFullPathTypeInferencer();
        $this->assertEquals( 'xml/root/element', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'element' )->item( 0 ) ) );
    }

    public function testInferenceFullPathType2()
    {
        $doc = new DOMDocument();
        $doc->loadXml( '<xml><root><element/></root></xml>' );

        $typeInferencer = new slFullPathTypeInferencer();
        $this->assertEquals( 'xml/root', $typeInferencer->inferenceType( $doc->getElementsByTagName( 'root' )->item( 0 ) ) );
    }
}

