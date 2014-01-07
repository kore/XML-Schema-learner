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

/*
 * Require environment file
 */
require __DIR__ . '/../src/environment.php';

/*
 * Require test suites.
 */
require 'main_suite.php';
require 'visitor_suite.php';

/**
 * General root test suite
 */
class slTestSuite extends PHPUnit_Framework_TestSuite
{
    /**
     * Basic constructor for test suite
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setName( 'XML-Schema-learner' );

        $this->addTestSuite( slMainTestSuite::suite() );
        $this->addTestSuite( slVisitorTestSuite::suite() );
    }

    /**
     * Return test suite
     * 
     * @return slTestSuite
     */
    public static function suite()
    {
        return new slTestSuite( __CLASS__ );
    }
}
