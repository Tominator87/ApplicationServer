<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision\ApplicationServer
 */

namespace TechDivision\ApplicationServer;


class InitialContext {
    
    /**
     * The cache instance, e. g. Memcached
     * @var \Memcached
     */
    protected $cache;

    /**
     * Factory method implementation.
     * 
     * @return \TechDivision\ApplicationServer\InitialContext The singleton instance
     */
    public static function get() {
        return new InitialContext();
    }
    
    /**
     * Initializes the context with the connection to the persistence
     * backend, e. g. Memcached
     * 
     * @return void
     */
    public function __construct() {
        $this->cache = new \Memcached();
        $this->cache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->cache->addServers(array(array('127.0.0.1', 11211)));        
    }
    
    /**
     * Reinitializes the context with the connection to the persistence
     * backend, e. g. Memcached
     */
    public function __wakeup() {
        $this->cache = new \Memcached();
        $this->cache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->cache->addServers(array(array('127.0.0.1', 11211))); 
    }
    
    /**
     * Stores the passed key value pair in the initial context.
     * 
     * @param string $key The key to store the value under
     * @param mixed $value The value to add to the inital context
     * @return mixed The value added to the initial context
     */
    public function setAttribute($key, $value) {
        $this->cache->set($key, $value);
        return $value;
    }
    
    /**
     * Returns the value with the passed key from the initial context.
     * 
     * @param string $key The key of the value to return
     * @return mixed The value stored in the initial context
     */
    public function getAttribute($key) {
        return $this->cache->get($key);
    }
    
    /**
     * Returns a reflection class intance for the passed class name.
     * 
     * @param string $className The class name to return the reflection instance for
     * @return \ReflectionClass The reflection instance
     */
    public function newReflectionClass($className) {
        return new \ReflectionClass($className);
    }
    
    /**
     * Returns a new instance of the passed class name.
     * 
     * @param string $className The fully qualified class name to return the instance for
     * @param array $args Arguments to pass to the constructor of the instance
     * @return object The instance itself
     */
    public function newInstance($className, array $args = array()) { 
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
    
    /**
     * Run's a lookup for the session bean with the passed class name and 
     * session ID. If the passed class name is a session bean an instance
     * will be returned.
     * 
     * @param string $className The name of the session bean's class
     * @param string $sessionId The session ID
     * @param array $args The arguments passed to the session beans constructor
     * @return object The requested session bean
     * @throws \Exception Is thrown if passed class name is no session bean
     */
    public function lookup($className, $sessionId, array $args = array()) {
        
        // get the reflection class for the passed class name
        $reflectionClass = $this->newReflectionClass($className);
        
        // if the class is a stateless session bean simply return a new instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Stateless')) {
            return $reflectionClass->newInstanceArgs($args);
        }
        
        // if the class is a statefull session bean, first check the container for a initialized instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Statefull')) {
            
            // load the session's from the initial context
            $session = $this->getAttribute($sessionId);
            
            // if an instance exists, load and return it
            if (is_array($session)) {              
                if (array_key_exists($className, $session)) {
                    return $session[$className];
                }
            } else {
                $session = array();
            }
            
            // if not, initialize a new instance, add it to the container and return it
            $instance = $reflectionClass->newInstanceArgs($args);           
            $session[$className] = $instance;           
            $this->setAttribute($sessionId, $session);        
            return $instance;
        }
        
        // if the class is a singleton session bean, return the singleton instance if available
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Singleton')) {
            
            // check if an instance is available
            if ($this->getAttribute($className)) {
                return $this->getAttribute($className);
            }
            
            // if not create a new instance and return it
            $instance = $reflectionClass->newInstanceArgs($args);            
            $this->setAttribute($className, $instance);           
            return $instance;
        }
        
        // if the class is no session bean, throw an exception
        throw new \Exception("Can\'t find session bean with class name '$className'");
    }
}