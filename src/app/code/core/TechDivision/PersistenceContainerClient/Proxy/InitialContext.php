<?php

/**
 * TechDivision\PersistenceContainerClient\Proxy\InitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Proxy;

use TechDivision\PersistenceContainerClient\Proxy;

/**
 * Proxy for the container instance itself.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class InitialContext extends Proxy {

    /**
     * Initialize the proxy instance.
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct('TechDivision\ApplicationServer\InitialContext');
    }

    /**
     * Runs a lookup on the container for the class with the
     * passed name.
     * 
     * @param string $className The class name to run the lookup for
     * @return TechDivision\PersistenceContainerClient\Interfaces\RemoteObject The instance
     */
    public function lookup($className) {
        return Proxy::create($className)->setSession($this->getSession());
    }

}