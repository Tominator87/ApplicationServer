<?php

/**
 * TechDivision\Example\MessageBeans\LogTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\MessageBeans;

/**
 *
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class LogTest implements MessageBean {
	
	/**
	 * MessageBean is not started.
	 * @var integer
	 */
	const STOPPED = 0;
	
	/**
	 * MessageBean is started.
	 * @var integer
	 */
	const STARTED = 1;
	
	/**
	 * The worker instance.
	 * @var \Worker
	 */
	protected $worker = null;
	
	/**
	 * The startup time.
	 * @var integer
	 */
	protected $startupTime = 0;
	
	/**
	 * The state of the MessageBean.
	 * @var integer
	 */
	protected $state = LogTest::STOPPED;
	
	/**
	 * Initializes the receiver with the initializing 
	 * TalkbackHandler.
	 * 
	 * @param \Worker $worker The initializing QueueWorker instance
	 * @return void
	 */
	public function __construct(\Worker $Worker) {
		// initialize the MessageBean with the passed values
		$this->worker = $worker;
	}
	
	/**
	 * @see MessageBean::start()
	 */
	public function start() {
		// initialize the startup and the last invokation time
		$this->startupTime = time();
		$this->state = LogTest::STARTED;
	}
	
	/**
	 * @see MessageBean::perform($timeOfCall, $remainingRepetitions)
	 */
	public function perform($timeOfCall, $remainingRepetitions) {			
		error_log("Invoking " .  __METHOD__ . " at " . date("Y-m-d H:i:s", $timeOfCall) . " remaining $remainingRepetitions");	
	}
	
	/**
	 * @see MessageBean::stop()
	 */
	public function stop() {			
		$this->startupTime = 0;
		$this->state = LogTest::STOPPED;
	}
	
	/**
	 * @see MessageBean::isStarted()
	 */
	public function isStarted() {
		return $this->state == LogTest::STARTED;
	}
}