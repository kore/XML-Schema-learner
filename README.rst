==================
XML Schema learner
==================

This software implements various state-of-the-art algorithms for algorithmic
learning of XML Schema definitions and Document Type Definitions.

Given a set or a single XML instance it can algorithmically infer a schema
which describes the XML instances. The resulting schemas are of high-quality
and human readable.

Usage
=====

To learn about the usage of the tool type::

    $ ./schema-learn --help

To run the unit tests type::

    $ phpunit tests/suite.php

Documentation
=============

On of the biggest issues existing tools (like trang) fight with is that
learning human readable regular expressions for child patterns is
nontrivial. Recently there have been some interesting new algorithms developed
that allow us to infer sane, human-readable regular expressions -- these are
implemented in XML-Schema-learner.

What do regular expressions have to do with XML schemas? Regular expressions
don't only operate on bytes (or UTF-8 characters in PCRE), but also on other
things, like XML elements. In DTDs, for example, specify which elements may
occur in another element using regular expressions::

    <!ELEMENT dl (dt|dd)+>

Here we have a regular expression ``(dt|dd)+`` for the elements which may occur
directly in ``dl``. A regular expressions, like ``(dt, dd*)+``, would for
example mean, that there may be any non-zero number of ``dt`` elements, each
followed by any number of ``dd`` elements.

An Example
----------

An example XML file is provided as ``examples/multitype.xml``::

    $ cat examples/multitype.xml
    <shop>
        <sale>
            <item id="23">
                <name>Some stuff</name>
                <price currency="EUR">23.42</price>
            </item>
            <item id="42">
                <name>Some other stuff</name>
                <price currency="EUR">42.23</price>
            </item>
        </sale>
        <stock>
            <item id="23">
                <amount>456</amount>
            </item>
            <item id="42">
                <amount>123</amount>
            </item>
        </stock>
    </shop>

Let's see how we can generate a DTD from this sample::

    $ ./schema-learn examples/multitype.xml
    <!ELEMENT name (#PCDATA)>
    <!ELEMENT price (#PCDATA)>
    <!ELEMENT item ( ( amount | ( name, price ) ) )>
    <!ELEMENT sale ( item* )>
    <!ELEMENT amount (#PCDATA)>
    <!ELEMENT stock ( item* )>
    <!ELEMENT shop ( ( sale, stock ) )>

    <!ATTLIST price currency CDATA #REQUIRED>
    <!ATTLIST item id CDATA #REQUIRED>

You can see a human readable DTD schema for the XML above. For such trivial
cases, all available tools will provide you with good results. But
XML-Schema-learner should produce human-readable output even in more difficult
cases, where other tools fail.

From DTDs to XML Schema
-----------------------

The difference between DTD and XML Schema is not just syntax. XML Schema has a
richer syntax for regular expressions, but more importantly, it has a different
typing mechanism that exceeds the capabilities of DTD. In XML Schema it is
possible to have elements with the same name using a different type if they are
located at different places in your XML tree.

See the ``<item>`` element above, which differs depending on the parent
element. With XML Schema you can use two different types, making your schema a
lot more specific. Additionally you can reuse the same type for elements with
different names. So that, for example, ``<price>`` and ``<amount>`` could both
refer to a type number.

The XML-Schema-learner can now learn schemas using the semantics of DTD and
simply format them as XML Schema. But it can also learn full-blown XML Schema
definitions. Here the situation gets a bit more complicated; this is described
in the thesis "Algorithmic learning of XML Schema definitions from XML data"
available from
http://kore-nordmann.de/talks/11_03_learning_xml_schema_definitions_from_xml_data.pdf.

It is not easy to decide if two slightly different types in different locations
of the XML tree should be considered one type or two. Since you seldom have XML
data expressing all allowed variants of your "virtual" schema you might not
want to be too strict when making the decision.

There is no sane default, though, which is why the tool offers you several ways
to configure the locality (how many parent elements should be taken into
account to potentially tell different types apart) and different comparators
for merging the types. For the simple example above just setting the locality
to 1 works well, and results in a more specific schema, since the item types do
not occur anywhere else in the tree and thus do not need merging at all::

    ./schema-learn -t xsd --locality 1 examples/multitype.xml
    <?xml version="1.0"?>
    <!-- ... -->
    <complexType name="sale/item">
      <sequence>
        <element name="name" type="string"/>
        <element name="price" type="item/price"/>
      </sequence>
      <attribute name="id" type="string" use="required"/>
    </complexType>
    <!-- ... -->
    <complexType name="stock/item">
      <element name="amount" type="string"/>
      <attribute name="id" type="string" use="required"/>
    </complexType>
    <!-- ... -->

As you can see two different types have been learned for the two different
definitions of the ``<item>`` element.

To learn more about the comparators and how they affect the schema learning
process, please read the aforementioned thesis, "Algorithmic learning of XML
Schema definitions from XML data" available from
http://kore-nordmann.de/talks/11_03_learning_xml_schema_definitions_from_xml_data.pdf.

Further documentation of the algorithms and which algorithms fit which use
cases is pending. The implementations refer to papers describing the algorithms
in their respective documentation.

Installation
============

A makefile is provided for system installations. By default, the program will
install to ``/usr/local``::

    $ make install

This can be changed by the ``PREFIX`` variable. A package manager might want to
install directly into ``/usr``::

    $ make PREFIX=/usr install

``DESTDIR`` is also supported; for more information see,

  1. http://www.gnu.org/prep/standards/html_node/DESTDIR.html

  2. http://www.freebsd.org/doc/en/books/porters-handbook/porting-prefix.html



..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
