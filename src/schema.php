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
 * Class representing a schema.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
abstract class slSchema
{
    /**
     * Array of automatons
     *
     * Contains a distinct automaton for each found type
     * 
     * @var array
     */
    protected $automatons = array();

    /**
     * Schema structure
     *
     * Inferenced scema structure consisting of types and their associated 
     * regular expressions.
     * 
     * @var array
     */
    protected $expressions = array();

    /**
     * Construct new schema class
     * 
     * @return void
     */
    public function __construct()
    {
        $this->automatons  = array();
        $this->expressions = array();
    }

    /**
     * Inference type from DOMElement
     * 
     * @param DOMElement $element 
     * @return void
     */
    abstract protected function inferenceType( DOMElement $element );

    /**
     * Learn XML file
     *
     * Learn the automaton from an XML file
     * 
     * @param string $file 
     * @return void
     */
    public function learnFile( $file )
    {
        $doc = new DOMDocument();
        $doc->load( $file );
        $this->traverse( $doc );
    }

    /**
     * Get regular expressions for learned schema
     *
     * Get an array of type -> regular expression associations for the learned 
     * schema.
     * 
     * @return void
     */
    public function getRegularExpressions()
    {
        foreach ( $this->automatons as $type => $automaton )
        {
            $this->expressions[$type] = $this->convertRegularExpression( $automaton );
        }

        return $this->expressions;
    }

    /**
     * Lear Automaton for element
     * 
     * @param DOMElement $element 
     * @param array $children 
     * @return void
     */
    protected function learnAutomaton( DOMElement $element, array $children )
    {
        $type = $this->inferenceType( $element );

        if ( !isset( $this->automatons[$type] ) )
        {
            $this->automatons[$type] = new slSingleOccurenceAutomaton();
        }

        $elementNames = array();
        foreach ( $children as $child )
        {
            // @TODO: make this namespace aware
            $elementNames[] = $child->tagName;
        }

        $this->automatons[$type]->learn( $elementNames );
    }

    /**
     * Traverse XML tree
     *
     * Traverses the XML tree and calls the learnAutomaton() method for each 
     * element, with its child element nodes.
     * 
     * @param DOMNode $root 
     * @return void
     */
    protected function traverse( DOMNode $root )
    {
        $elements = array();
        foreach ( $root->childNodes as $node )
        {
            if ( $node->nodeType !== XML_ELEMENT_NODE )
            {
                continue;
            }

            $elements[] = $node;
            $this->traverse( $node );
        }

        if ( ( $root->nodeType === XML_ELEMENT_NODE ) &&
             count( $elements ) )
        {
            $this->learnAutomaton( $root, $elements );
        }
    }

    /**
     * Convert automaton to regular expression
     * 
     * @param slAutomaton $automaton 
     * @return slRegularExpression
     */
    protected function convertRegularExpression( $automaton )
    {
        // Convert automatons
        $converter = new slSoreConverter();
        return $converter->convertAutomaton( $automaton );
    }
}
 
