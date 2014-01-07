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
class slMainRegularExpressionTests extends PHPUnit_Framework_TestCase
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

    public function testCreateSingularRegularExpression()
    {
        $regexp = new slRegularExpressionOptional(
            $c = new slRegularExpressionElement( 'a' )
        );

        $this->assertSame( $c, $regexp->getChild() );
    }

    public function testSingularSetChild()
    {
        $regexp = new slRegularExpressionOptional(
            new slRegularExpressionElement( 'a' )
        );
        $regexp->setChild( $c = new slRegularExpressionElement( 'b' ) );

        $this->assertSame( $c, $regexp->getChild() );
    }

    public function testSingularSetChildren()
    {
        $regexp = new slRegularExpressionOptional(
            new slRegularExpressionElement( 'a' )
        );
        $regexp->setChildren( array( $c = new slRegularExpressionElement( 'b' ), new slRegularExpressionElement( 'c' ) ) );

        $this->assertSame( $c, $regexp->getChild() );
    }

    public function testSingularGetChildren()
    {
        $regexp = new slRegularExpressionOptional(
            new slRegularExpressionElement( 'a' )
        );
        $regexp->setChild( $c = new slRegularExpressionElement( 'b' ) );

        $this->assertSame( array( $c ), $regexp->getChildren() );
    }

    public function testCreateMultipleRegularExpression()
    {
        $regexp = new slRegularExpressionSequence(
            $c = new slRegularExpressionElement( 'a' )
        );

        $this->assertSame( array( $c ), $regexp->getChildren() );
    }

    public function testMultipleSetChildren()
    {
        $regexp = new slRegularExpressionSequence(
            new slRegularExpressionElement( 'a' )
        );
        $regexp->setChildren( array( 
            $c1 = new slRegularExpressionElement( 'b' ), 
            $c2 = new slRegularExpressionElement( 'c' ),
        ) );

        $this->assertSame( array( $c1, $c2 ), $regexp->getChildren() );
    }

    public function testMultipleSetChildrenFail()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        try
        {
            $regexp->setChildren( array( 
                new StdClass(),
            ) );
            $this->fail( 'Expected exception.' );
        } catch ( Exception $e )
        { /* Expected */ }
    }

    public function testMultipleOffsetExists()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        $this->assertTrue( isset( $regexp[0] ) );
        $this->assertFalse( isset( $regexp[2] ) );
    }

    public function testMultipleOffsetGet()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        $this->assertSame( $c1, $regexp[0] );
    }

    public function testMultipleOffsetGetFail()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        try
        {
            $regexp[42];
            $this->fail( 'Expected exception.' );
        } catch ( Exception $e )
        { /* Expected */ }
    }

    public function testMultipleOffsetSet()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        $this->assertSame( $c1, $regexp[0] );
        $regexp[0] = $c2;
        $this->assertSame( $c2, $regexp[0] );
    }

    public function testMultipleOffsetUnset()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        $this->assertTrue( isset( $regexp[0] ) );
        unset( $regexp[0] );
        $this->assertFalse( isset( $regexp[0] ) );
    }

    public function testMultipleOffsetUnsetFail()
    {
        $regexp = new slRegularExpressionSequence(
            $c1 = new slRegularExpressionElement( 'a' ), 
            $c2 = new slRegularExpressionElement( 'b' )
        );

        try
        {
            unset( $regexp[42] );
            $this->fail( 'Expected exception.' );
        } catch ( Exception $e )
        { /* Expected */ }
    }

    public function testCreateElement()
    {
        $regexp = new slRegularExpressionElement( 'a' );

        $this->assertSame( 'a', $regexp->getContent() );
    }

    public function testElementSetContent()
    {
        $regexp = new slRegularExpressionElement( 'a' );
        $regexp->setContent( 'b' );

        $this->assertSame( 'b', $regexp->getContent() );
    }

    public function testElementToString()
    {
        $regexp = new slRegularExpressionElement( 'a' );

        $this->assertSame( 'a', (string) $regexp );
    }
}

