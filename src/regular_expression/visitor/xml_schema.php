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
 * Regular expression string visitor
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slRegularExpressionXmlSchemaVisitor extends slRegularExpressionVisitor
{
    /**
     * XML Schema schema visitor, which provides the implementation for XML 
     * Schema specifc simple type conversions.
     * 
     * @var slSchemaXmlSchemaVisitor
     */
    protected $schemaVisitor;

    /**
     * Owner document for the generated regular expression markup
     * 
     * @var DOMDocument
     */
    protected $document;

    /**
     * Construct visitor
     *
     * Construct the visitor from the document, for which the regular 
     * expressions should be created.
     * 
     * @param DOMDocument $document 
     * @return void
     */
    public function __construct( slSchemaXmlSchemaVisitor $schemaVisitor, DOMDocument $document )
    {
        $this->schemaVisitor = $schemaVisitor;
        $this->document      = $document;
    }

    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionEmpty $element 
     * @return mixed
     */
    protected function visitEmpty( slRegularExpressionEmpty $element )
    {
        return $this->document->createComment( 'Empty regular expression' );
    }

    /**
     * Visit single element in regular expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionElement $element 
     * @return mixed
     */
    protected function visitElement( slRegularExpressionElement $element )
    {
        $schemaElement = $this->schemaVisitor->getType( $element->getContent()->type );
        $node = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'element' );
        $node->setAttribute( 'name', $element->getContent()->name );

        if ( ( $schemaElement->type->regularExpression instanceof slRegularExpressionEmpty ) &&
             ( !count( $schemaElement->type->attributes ) ) )
        {
            if ( $schemaElement->type->simpleTypeInferencer->inferenceType() !== 'empty' )
            {
                $node->setAttribute( 'type', $this->schemaVisitor->getXmlSchemaSimpleType( $schemaElement->type->simpleTypeInferencer ) );
            }
        }
        else
        {
            $node->setAttribute( 'type', $schemaElement->type->name );
        }

        return $node;
    }

    /**
     * Visit choice sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionChoice $regularExpression 
     * @return mixed
     */
    protected function visitChoice( slRegularExpressionChoice $regularExpression )
    {
        $choice = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'choice' );
        foreach ( $regularExpression->getChildren() as $child )
        {
            $choice->appendChild( $this->visit( $child ) );
        }

        return $choice;
    }

    /**
     * Visit sequence sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    protected function visitSequence( slRegularExpressionSequence $regularExpression )
    {
        $sequence = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'sequence' );
        foreach ( $regularExpression->getChildren() as $child )
        {
            $sequence->appendChild( $this->visit( $child ) );
        }

        return $sequence;
    }

    /**
     * Visit all sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionSequence $regularExpression 
     * @return mixed
     */
    protected function visitAll( slRegularExpressionAll $regularExpression )
    {
        $all = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'all' );
        foreach ( $regularExpression->getChildren() as $child )
        {
            $all->appendChild( $this->visit( $child ) );
        }

        if ( $regularExpression->minOccurences === 0 )
        {
            $all->setAttribute( 'minOccurs', '0' );
        }

        return $all;
    }

    /**
     * Visit optional sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionOptional $regularExpression 
     * @return mixed
     */
    protected function visitOptional( slRegularExpressionOptional $regularExpression )
    {
        $sequence = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'sequence' );
        $sequence->setAttribute( 'minOccurs', '0' );
        $sequence->setAttribute( 'maxOccurs', '1' );
        $sequence->appendChild( $this->visit( $regularExpression->getChild() ) );

        return $sequence;
    }

    /**
     * Visit repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeated $regularExpression 
     * @return mixed
     */
    protected function visitRepeated( slRegularExpressionRepeated $regularExpression )
    {
        $sequence = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'sequence' );
        $sequence->setAttribute( 'minOccurs', '0' );
        $sequence->setAttribute( 'maxOccurs', 'unbounded' );
        $sequence->appendChild( $this->visit( $regularExpression->getChild() ) );

        return $sequence;
    }

    /**
     * Visit at least once repeated sub expression
     *
     * The return type of this method varies deping on the concrete visitor 
     * implementation
     * 
     * @param slRegularExpressionRepeatedAtLeastOnce $regularExpression 
     * @return mixed
     */
    protected function visitRepeatedAtLeastOnce( slRegularExpressionRepeatedAtLeastOnce $regularExpression )
    {
        $sequence = $this->document->createElementNS( 'http://www.w3.org/2001/XMLSchema', 'sequence' );
        $sequence->setAttribute( 'minOccurs', '1' );
        $sequence->setAttribute( 'maxOccurs', 'unbounded' );
        $sequence->appendChild( $this->visit( $regularExpression->getChild() ) );

        return $sequence;
    }
}

