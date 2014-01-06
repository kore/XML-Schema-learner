==================
XML Schema learner
==================

.. image:: https://travis-ci.org/kore/XML-Schema-learner.png?branch=master
   :target: https://travis-ci.org/kore/XML-Schema-learner

This software implements various state-of-the-art algorithms for algorithmic
learning of XML Schema definitions and Document Type Definitions.

Given a set or a single XML instance it can algorithmically infer a schema
which describes the XML instances. The resulting schemas are of high-quality
and human readable.

Usage
=====

To learn about the usage of the tool type::

    ./learn --help

To run the unit tests type::

    phpunit tests/suite.php

Documentation
=============

Further documentation of the algorithms and which algorithms fit which use
cases is pending. The implementations refer to papers describing the algorithms
in their respective documentation.


..
   Local Variables:
   mode: rst
   fill-column: 79
   End: 
   vim: et syn=rst tw=79
