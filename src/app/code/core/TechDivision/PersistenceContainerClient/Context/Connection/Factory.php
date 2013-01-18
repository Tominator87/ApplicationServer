<?php

/**
 * TechDivision\PersistenceContainerClient\Context\Connection\Factory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\PersistenceContainerClient\Context\Connection;

/**
 * Connection factory to create a new context connection.
 *
 * @package     TechDivision\PersistenceContainerClient
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Factory {

    /**
     * The instance as singleton.
     * @var \TechDivision\PersistenceContainerClient\Interfaces\Connection 
     */
    protected static $instance = null;

    /**
     * Simple factory to create a new context connection 
     * of the requested type.
     * 
     * @param string $type The context connection type to create
     * @return \TechDivision\PersistenceContainerClient\Interfaces\Connection The requested context connection
     */
    public static function createContextConnection($type = 'Socket') {
        
        if (self::$instance == null) {
            $className = "TechDivision\PersistenceContainerClient\Context\ContextConnection$type";
            $reflectionClass = new \ReflectionClass($className);
            self::$instance = $reflectionClass->newInstance();
        }
        
        return self::$instance;
    }

}