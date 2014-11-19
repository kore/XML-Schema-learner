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
        $typeInferencer = new slNameBasedTypeInferencer();
        $this->assertEquals(
            'element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceNameBasedType2()
    {
        $typeInferencer = new slNameBasedTypeInferencer();
        $this->assertEquals(
            'xml',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
            ) )
        );
    }

    public function testInferenceKLocalType0()
    {
        $typeInferencer = new slKLocalTypeInferencer( 0 );
        $this->assertEquals(
            'element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceKLocalType1()
    {
        $typeInferencer = new slKLocalTypeInferencer( 1 );
        $this->assertEquals(
            'root/element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceKLocalType2()
    {
        $typeInferencer = new slKLocalTypeInferencer( 2 );
        $this->assertEquals(
            'xml/root/element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceKLocalType3()
    {
        $typeInferencer = new slKLocalTypeInferencer( 3 );
        $this->assertEquals(
            'xml/root/element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceFullPathType1()
    {
        $typeInferencer = new slFullPathTypeInferencer();
        $this->assertEquals(
            'xml/root/element',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'element',
                ),
            ) )
        );
    }

    public function testInferenceFullPathType2()
    {
        $typeInferencer = new slFullPathTypeInferencer();
        $this->assertEquals(
            'xml/root',
            $typeInferencer->inferenceType( array(
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'xml',
                ),
                array(
                    'namespace' => 'http://example.com/ns1',
                    'name'      => 'root',
                ),
            ) )
        );
    }

    /**
     * Get simple PCDATA types
     *
     * @return array
     */
    public function getSimplePcdataTypes()
    {
        return array(
            array('string', 'PCDATA'),
            array('false', 'PCDATA'),
            array('0', 'PCDATA'),
            array("\n", 'PCDATA'),
            array('', 'empty'),
        );
    }

    /**
     * @dataProvider getSimplePcdataTypes
     */
    public function testInferenceSimplePcdataTypes($string, $type)
    {
        $typeInferencer = new slPcdataSimpleTypeInferencer();
        $typeInferencer->learnString($string);
        $this->assertEquals($type, $typeInferencer->inferenceType());
    }
}

