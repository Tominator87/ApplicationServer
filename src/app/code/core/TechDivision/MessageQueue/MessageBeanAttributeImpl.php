<?php

/**
 * TechDivision\MessageQueue\MessageBeanAttributeImpl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\MessageQueue;

use TechDivision\MessageQueue\Interfaces\MessageBeanAttribute;

/**
 * @package     TechDivision\MessageQueue
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class MessageBeanAttributeImpl implements MessageBeanAttribute {
	
	/**
	 * The key for the class name of the MessageBean.
	 * @var string
	 */
	const SCHEDULABLE_CLASS = "SchedulableClass";
	
	/**
	 * The key for the arguments passed to the MessageBean when instanciated.
	 * @var string
	 */
	const SCHEDULABLE_ARGUMENTS = "SchedulableArguments";
	
	/**
	 * The key for the argument types passed to the MessageBean when instanciated.
	 * @var string
	 */
	const SCHEDULABLE_ARGUMENT_TYPES = "SchedulableArgumentTypes";
	
	/**
	 * The key for the initial start date of the MessageBean (Unix Timestamp).
	 * @var string
	 */
	const INITIAL_START_DATE = "InitialStartDate";
	
	/**
	 * The key for the initial repetition of the MessageBean, -1 for endless repetitions.
	 * @var string
	 */
	const INITIAL_REPETITIONS= "InitialRepetitions";
	
	/**
	 * The key for scheduling period in seconds of the MessageBean.
	 * @var string
	 */
	const SCHEDULE_PERIOD = "SchedulePeriod";
	
	/**
	 * The key to expect a boolean value to invoke the MessageBean's start method when initializing the MessageQueue.
	 * @var string
	 */
	const START_AT_STARTUP = "StartAtStartup";
	
	/**
	 * The attribute's name.
	 * @var string
	 */
	protected $name = "";
	
	/**
	 * The attributes value.
	 * @var string
	 */
	protected $value = "";
	
	/**
	 * Private constructor to force using the 
	 * factory method for a new instance.
	 *
	 * @return void
	 */
	private final function __construct() { /* Class is a utility class */ }
	
	/**
	 * Factory method.
	 * 
	 * @return MessageBeanAttributeImpl The new instance
	 */
	public static function get() {
		return new MessageBeanAttributeImpl();
	}
	
	/**
	 * Returns an array with allowed attributes.
	 * 
	 * @return array The array with the allowed attributes
	 */
	public static function getAvailableAttributes() {
		// initialize the array
		$availableAttributes = array(
			MessageBeanAttributeImpl::SCHEDULABLE_CLASS,
			MessageBeanAttributeImpl::SCHEDULABLE_ARGUMENTS,
			MessageBeanAttributeImpl::SCHEDULABLE_ARGUMENT_TYPES,
			MessageBeanAttributeImpl::INITIAL_START_DATE,
			MessageBeanAttributeImpl::INITIAL_REPETITIONS,
			MessageBeanAttributeImpl::SCHEDULE_PERIOD,
			MessageBeanAttributeImpl::START_AT_STARTUP
		);
		// return the array
		return $availableAttributes;
	}
	
	/**
	 * @see MessageBeanAttribute::getName()
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the attribute's name from the configuration file.
	 * 
	 * @param string $name The attribute name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @see MessageBeanAttribute::getValue()
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Sets the attribute's value from the configuration file.
	 * 
	 * @param string $value The attribute value
	 * @return void
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}