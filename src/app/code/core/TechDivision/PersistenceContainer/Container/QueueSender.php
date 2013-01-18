<?php

/**
 * TechDivision\PersistenceContainer\Container\QueueSender
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer\Container;

use TechDivision\ApplicationServer\AbstractSender;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class QueueSender extends AbstractSender {
    
    /**
     * The message queue resource
     * @var resource 
     */
    protected $queue;
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::prepare()
     */
    public function prepare($remoteMethod) {      
        // initialize the queue
        $this->queue = msg_get_queue($remoteMethod->getPort());
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::sendLine()
     */
    public function sendLine($data) {
        msg_send($this->queue, 1, $data, true, true);
    }
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\SenderInterface::close()
     */
    public function close() {
        // do nothing here
    }
}