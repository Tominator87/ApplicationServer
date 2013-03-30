<?php

/**
 * TechDivision\Example\MessageBeans\CacheCounterReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\Example\MessageBeans;

use TechDivision\MessageQueueClient\Interfaces\Message;
use TechDivision\MessageQueueClient\Receiver\AbstractReceiver;
use TechDivision\MessageQueueClient\Messages\MessageMonitor;
use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

/**
 * This is the implementation of a import message receiver.
 *
 * @package     TechDivision\Example
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ImportReceiver extends AbstractReceiver {
	
	/**
	 * @see \TechDivision\MessageQueueClient\Interfaces\MessageReceiver::onMessage()
	 */
	public function onMessage(Message $message, $sessionId) {
		
	    // log that a Message was received
		error_log($logMessage = "Successfully received / finished message");
		$connection = Factory::createContextConnection();
		$session = $connection->createContextSession();
		$initialContext = $session->createInitialContext();
		
		// lookup the remote processor implementation
		$processor = $initialContext->lookup('TechDivision\Example\Services\SampleProcessor');
		
		// define the import file
		$importFile = 'webapps/example/META-INF/data/example-persons.csv';
		
		// open the import file
		$persons = file($importFile, FILE_USE_INCLUDE_PATH);
		error_log("Found " . sizeof($persons) . " persons in CSV file '$importFile'");
		
		// import the persons found in the file
		foreach ($persons as $person) {
		    
		    // explode the name parts and append the persons in the database
		    list ($firstname, $lastname) = explode(',', $person);
		    
		    $entity = new Sample();
		    $entity->setName($firstname . ', ' . $lastname);
		    
		    $sampleId = $processor->persist($entity);
		    
		    error_log("Successfully updated person with name: '{$entity->getName()}'");
		}
		
		// initialize the message monitor
		$message->setMessageMonitor($monitor = new MessageMonitor(1, $logMessage));
		$monitor->setRowCount(1);
		
		// update the MessageMonitor
		$this->updateMonitor($message);
	}
}