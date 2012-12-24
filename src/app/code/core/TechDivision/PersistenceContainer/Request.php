<?php

/**
 * TechDivision_PersistenceContainer_Request
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainer;

/**
 * @package     TechDivision\PersistenceContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Request extends \Stackable {
    
    /**
     * The serialized remote method call.
     * @var string
     */
    public $line;
    
    /**
     * Initializes the request with the serialized remote method data.
     * 
     * @param string $line The serialized remote method data 
     */
    public function __construct($line) {
        $this->line = $line;
    }
    
    /**
     * @see \Stackable::run()
     */
    public function run() {
        // check if a worker is available
        if ($this->worker) {
            // process the request
            $this->worker->processRequest($this->line);
            // notify the calling thread
            $this->notify();
        }
    }
}