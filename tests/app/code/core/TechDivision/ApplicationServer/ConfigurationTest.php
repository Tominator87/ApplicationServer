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
	 * Test if a manually added configuration instance
	 * will has been added correctly.
	 *
	 * @return void
	 */
	public function testHasChildrenByAddingOneManually() {
		$child = new Configuration('foo');
		$this->configuration->addChild($child);
		$this->assertTrue($this->configuration->hasChildren());
	}
	
	/**
	 * Test if a manually added configuration instance
	 * will has been added correctly.
	 *
	 * @return void
	 */
	public function testGetChildrenByAddingOneManually() {
		$child = new Configuration('foo');
		$this->configuration->addChild($child);
		$this->assertSame(array($child), $this->configuration->getChildren());
	}
	
	/**
	 * Test if a configuration init with a SimpleXMLElement
	 * has been added correctly.
	 *
	 * @return void
	 */
	public function testHasChildrenByInitWithSimpleXmlElement() {
		$this->configuration->init($this->getTestNode());
		$this->assertTrue($this->configuration->hasChildren());
	}
	
	/**
	 * Test if a configuration init with a SimpleXMLElement
	 * has been added correctly.
	 *
	 * @return void
	 */
	public function testGetChildrenByInitWithSimpleXmlElement() {
		$this->configuration->init($this->getTestNode());
		$toBeTested = new Configuration('testnode');
		$toBeTested->setValue('test');
		$this->assertEquals(array($toBeTested), $this->configuration->getChildren());
	}
	
	/**
	 * Creates a SimpleXMLElement representing a test
	 * configuration element.
	 *
	 * @return SimpleXMLElement The test configuration element
	 */
	protected function getTestNode() {
		return new \SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>
			 <test>
			   <testnode value="test"/>
			 </test>'
		);
	}
}