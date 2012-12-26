<?php

/**
 * TechDivision_ApplicationServer_Interfaces_ContainerInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer\Interfaces;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface ContainerInterface {
    
    /**
     * Returns the maximum number of workers to start.
     * 
     * @return integer The maximum worker number 
     */
    public function getWorkerNumber();

    /**
     * Main method that starts the server.
     * 
     * @return void
     */
    public function start();
    
    /**
     * Returns the receiver instance ready to be started.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function getReceiver();
    
    /**
     * Returns the sender instance ready to be prepared.
     * 
     * @return TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function getSender();
    
    /**
     * Stacks the passed remote method call (serialized) to one of the
     * internal workers and returns.
     * 
     * If enabled the garbage collection will be run and the configuration
     * will be refreshed.
     * 
     * @param string $line The serialized remote method call to stack
     * @return void
     */
    public function stack($line);
}