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
 * Main class, which implements the argument loading and dispatching to the 
 * learning implementations
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slMain
{
    /**
     * Used type inferencer
     * 
     * @var slTypeInferencer
     */
    protected $typeInferencer;

    /**
     * Array of automatons
     *
     * Contains a distinct automaton for each found type
     * 
     * @var array
     */
    protected $automatons = array();

    /**
     * Construct new main class
     * 
     * @param slTypeInferencer $typeInferencer 
     * @return void
     */
    public function __construct( slTypeInferencer $typeInferencer = null )
    {
        $this->typeInferencer = $typeInferencer === null ? new slNameBasedTypeInferencer() : $typeInferencer;

        $this->automatons = array();
    }

    /**
     * Start execution of learning process
     * 
     * @param array $argv 
     * @return void
     */
    public function main( array $argv )
    {
        array_shift( $argv );

        // Learn all provided files
        foreach ( $argv as $file )
        {
            $this->learnAutomatons( $file );
        }

        // Get regular expressions from automatons
        $expressions = array();
        foreach ( $this->automatons as $type => $automaton )
        {
            $expressions[$type] = $this->convertRegularExpression( $automaton );
        }

        return $expressions;
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
        $type = $this->typeInferencer->inferenceType( $element );

        if ( !isset( $this->automatons[$type] ) )
        {
            // @TODO: Make this injectable.
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
     * Learn automatons from given XML file
     * 
     * @param string $file 
     * @return void
     */
    protected function learnAutomatons( $file )
    {
        // Traverse whole XML, to find all DOMElement nodes
        $doc = new DOMDocument();
        $doc->load( $file );
        $this->traverse( $doc );
    }

    /**
     * Convert automaton to regular expression
     * 
     * @param slAutomaton $automaton 
     * @return void
     */
    protected function convertRegularExpression( $automaton )
    {
        // Convert automatons
        return $automaton;
    }
}
 
