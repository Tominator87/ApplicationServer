<?php 

/**
 * TechDivision\ApplicationServer\ConfigurationTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * The configuration instance to test.
	 * @var TechDivision\ApplicationServer\Configuration
	 */
	protected $configuration;

	/**
	 * Initializes the configuration instance to test.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new Configuration();
	}

	/**
	 * Tests the add child method.
	 *
	 * @return void
	 */
	public function testAddChild() {
		$child = new Configuration('foo');
		$this->configuration->addChild($child);
		$this->assertTrue($this->configuration->hasChildren());
	}
}