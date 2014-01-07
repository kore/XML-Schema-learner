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
 * Base class for visiting schemas
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slSchemaBonxaiVisitor extends slSchemaVisitor
{
    /**
     * Types, defined in processed schema, to return references to the types 
     * for sub-visitors, like the regular expression visitor.
     * 
     * @var array
     */
    protected $types = array();

    /**
     * Visit a schema
     *
     * The visitor is not structured, since the types might be required to be 
     * iterated tree-based for more complex schema definitions (like XML Schema 
     * schemas).
     *
     * The return value depends on the concrete visitor implementation.
     * 
     * @param slSchema $schema 
     * @return string
     */
    public function visit( slSchema $schema )
    {
        $doc = "namespace http://example.com/generated\n\n";

        $graph = $this->buildTypeGraph( $schema );
        $patterns = $this->getPatterns( $schema, $graph );

        $visitedTypes = array();
        $doc .= "grammar {\n";
        foreach ( ( $this->types = $schema->getTypes() ) as $type )
        {
            if ( !isset( $visitedTypes[$type->type->name] ) )
            {
                $doc .= $this->visitType( $type, $patterns[$type->type->name] );
                $visitedTypes[$type->type->name] = true;
            }
        }
        $doc .= "}\n";

        return $doc;
    }

    /**
     * Build type graph
     *
     * Builds a type graph from the schema type definitions and child patterns 
     * associated with the types.
     *
     * The type grapg represents a (information science) graph where each edge 
     * indicates that the dst type occurs within the src type.
     * 
     * @param slSchema $schema 
     * @return slTypeAutomaton
     */
    protected function buildTypeGraph( slSchema $schema )
    {
        $automaton = new slTypeAutomaton();

        // Add edges for root nodes
        foreach ( $schema->getRootElements() as $root )
        {
            $automaton->addEdge( '_start', $root, $root );
        }

        // Get contained children from each type and add edges for them to the 
        // graph
        foreach ( $schema->getTypes() as $element )
        {
            foreach ( $this->getChildElements( $element->type->regularExpression ) as $child )
            {
                $automaton->addEdge( $element->type->name, $child->type, $child->name );
            }
        }

        return $automaton;
    }

    /**
     * Get all elements occureing in a regular expression
     * 
     * @param slRegularExpression $regExp 
     * @return array
     */
    protected function getChildElements( slRegularExpression $regExp )
    {
        if ( $regExp instanceof slRegularExpressionElement )
        {
            return array( $regExp->getContent() );
        }

        if ( !$regExp instanceof slRegularExpressionContainer )
        {
            return array();
        }

        $children = array();
        foreach ( $regExp->getChildren() as $child )
        {
            $children = array_merge( $children, $this->getChildElements( $child ) );
        }
        return $children;
    }

    /**
     * Calculate patterns from type graph
     *
     * Calculates "nice" patterns from the specified type graph. Optimizations 
     * of those patterns might be outsourced into another class in the future.
     *
     * Returns an array with type-name => pattern associations.
     * 
     * @param slSchema $schema 
     * @param slTypeAutomaton $typeGraph 
     * @return array
     */
    protected function getPatterns( slSchema $schema, slTypeAutomaton $typeGraph )
    {
        $patterns = $typeGraph->getAncestorPatterns( $schema->getTypes() );
        return $patterns;
    }

    /**
     * Visit single type / element
     *
     * Vist a single element and create proper UPSL markup depending on the 
     * elements contents.
     * 
     * @param slSchemaElement $element 
     * @param mixed $ancestorPattern 
     * @return string
     */
    protected function visitType( slSchemaElement $element, $ancestorPattern )
    {
        $typeDef = "\t" . $this->visitAncestorPattern( $ancestorPattern ) . " = {\n";

        $typeDef .= $this->visitAttributeList( $element );

        $regExpVisitor = new slRegularExpressionBonxaiVisitor( $this, $root->ownerDocument );
        $typeDef .= "\t\t" . $regExpVisitor->visit( $element->type->regularExpression ) . "\n";

        return $typeDef . "\t}\n\n";
    }

    /**
     * Visit the ancestor pattern of a type
     * 
     * @param array $ancestorPattern
     * @return string
     */
    protected function visitAncestorPattern( array $ancestorPattern )
    {
        $stringPatterns = array();
        foreach ( $ancestorPattern as $pattern )
        {
            if ( reset( $pattern ) === '^' )
            {
                array_shift( $pattern );
                $string = '/';
            }
            else
            {
                $string = '//';
            }

            $stringPatterns[] = $string . implode( '/', $pattern );
        }

        if ( count( $stringPatterns ) === 1 )
        {
            return reset( $stringPatterns );
        }

        return '( ' . implode( ' | ', $stringPatterns ) . ' )';
    }

    /**
     * Visit the attribute list of an element.
     * 
     * @param slSchemaElement $element 
     * @return string
     */
    protected function visitAttributeList( slSchemaElement $element )
    {
        $attributeList = '';
        foreach ( $element->type->attributes as $attribute )
        {
            $attributeList .= sprintf( "\t\tattribute %s { %s }%s,\n",
                $attribute->name,
                $this->getBonxaiSimpleType( $attribute->simpleTypeInferencer ),
                $attribute->optional ? '?' : ''
            );
        }

        return $attributeList;
    }

    /**
     * Get XML Schema simple type
     *
     * Return an XML Schema simple type specification from the inferenced type 
     * provided by the given simple type inferencer.
     * 
     * @param slSimpleTypeInferencer $typeInferencer 
     * @return string
     */
    public function getBonxaiSimpleType( slSimpleTypeInferencer $typeInferencer )
    {
        switch ( $type = $typeInferencer->inferenceType() )
        {
            case 'PCDATA':
            case 'empty':
                return 'string';

            default:
                // @todo: Throw proper exception
                throw new RuntimeException( "Unhandled inferenced simple type '$type'." );
        }
    }

    /**
     * Get element type definition for type identifier
     *
     * Returns the element type definition, specified in a slSchemaElement 
     * object for the given type identifier.
     * 
     * @param string $type 
     * @return slSchemaElement
     */
    public function getType( $type )
    {
        return $this->types[$type];
    }

    /**
     * Set types array
     *
     * Method only used for testing, to set the contained types in the schema.
     * 
     * @param array $types
     * @return void
     * @access private
     */
    public function setTypes( array $types )
    {
        $this->types = $types;
    }
}

