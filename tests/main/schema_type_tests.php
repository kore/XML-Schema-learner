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
class slMainSchemaTypeTests extends PHPUnit_Framework_TestCase
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

    public function testCreateSchemaType()
    {
        $type = new slSchemaType( 't1' );
        $this->assertSame( 't1', $type->name );
    }

    public function testSetName()
    {
        $type = new slSchemaType( 't1' );
        $type->name = 't2';
        $this->assertSame( 't2', $type->name );
    }

    public function testGetUnknownProperty()
    {
        $type = new slSchemaType( 't1' );
        try
        {
            $type->unknwonProperty;
            $this->fail( 'Expected exception.' );
        } catch ( Exception $e )
        { /* Expected */ }
    }

    public function testSetUnknownProperty()
    {
        $type = new slSchemaType( 't1' );
        try
        {
            $type->unknwonProperty = 42;
            $this->fail( 'Expected exception.' );
        } catch ( Exception $e )
        { /* Expected */ }
    }

    public function testLearnAttributes()
    {
        $type = new slSchemaType( 't1' );

        $type->learnAttributes( array(
            'att1' => '42',
            'att2' => '23',
        ) );
        
        $this->assertSame(
            array( 'att1', 'att2' ),
            array_keys( $type->attributes )
        );
    }

    public function testLearnRequiredAttribute()
    {
        $type = new slSchemaType( 't1' );

        $type->learnAttributes( array(
            'att1' => '42',
            'att2' => '23',
        ) );
        $type->learnAttributes( array(
            'att1' => '23',
        ) );
     
        $this->assertFalse( $type->attributes['att1']->optional );
    }

    public function testLearnOptionalAttribute()
    {
        $type = new slSchemaType( 't1' );

        $type->learnAttributes( array(
            'att1' => '42',
            'att2' => '23',
        ) );
        $type->learnAttributes( array(
            'att1' => '23',
        ) );
     
        $this->assertTrue( $type->attributes['att2']->optional );
    }

    public function testLearnOptionalAttribute2()
    {
        $type = new slSchemaType( 't1' );

        $type->learnAttributes( array() );
        $type->learnAttributes( array(
            'att1' => '23',
        ) );
     
        $this->assertTrue( $type->attributes['att1']->optional );
    }

    public function testLearnNewOptionalAttribute()
    {
        $type = new slSchemaType( 't1' );

        $type->learnAttributes( array(
            'att1' => '42',
        ) );
        $type->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );
     
        $this->assertTrue( $type->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes1()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertFalse( $t1->attributes['att1']->optional );
        $this->assertFalse( $t1->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes2()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
        ) );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertTrue( $t1->attributes['att1']->optional );
        $this->assertTrue( $t1->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes3()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att2' => '23',
        ) );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertTrue( $t1->attributes['att1']->optional );
        $this->assertTrue( $t1->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes4()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '23',
        ) );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertFalse( $t1->attributes['att1']->optional );
        $this->assertTrue( $t1->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes5()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );
        $t1->learnAttributes( array() );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertTrue( $t1->attributes['att1']->optional );
        $this->assertTrue( $t1->attributes['att2']->optional );
    }

    public function testMergeTypeAttributes6()
    {
        $t1 = new slSchemaType( 't1' );
        $t1->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );

        $t2 = new slSchemaType( 't2' );
        $t2->learnAttributes( array(
            'att1' => '23',
            'att2' => '23',
        ) );
        $t2->learnAttributes( array() );
     
        $t1->merge( $t2 );
        $this->assertSame( array( 'att1', 'att2' ), array_keys( $t1->attributes ) );
        $this->assertTrue( $t1->attributes['att1']->optional );
        $this->assertTrue( $t1->attributes['att2']->optional );
    }
}

