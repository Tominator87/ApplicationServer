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
 * @package TechDivision\PersistenceContainer
 */
    
namespace TechDivision\PersistenceContainer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * The application instance holds all information about the deployed application
 * and provides a reference to the entity manager and the initial context.
 *
 * @package TechDivision\PersistenceContainer
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class Application {
    
    /**
     * The unique application name.
     * @var string
     */
    protected $name;
    
    /**
     * The path to the doctrine entities.
     * @var string
     */
    protected $pathToEntities;
    
    /**
     * The doctrine entity manager.
     * @var \Doctrine\Common\Persistence\ObjectManager 
     */
    protected $entityManager;
    
    /**
     * Array with the connection parameters.
     * @var array
     */
    protected $connectionParameters;
    
    /**
     * Passes the application name That has to be the class namespace.
     * 
     * @param type $name The application name
     */
    public function __construct($name) {
        $this->name = $name;
    }
    
    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     * 
     * @return \TechDivision\PersistenceContainer\Application The connected application
     */
    public function connect() {
        
        // initialize the Doctrine EntityManager instance
        $pathToEntities = $this->getPathToEntities();
        
        // load the doctrine metadate information
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($pathToEntities, true);
        
        // load the connection parameters
        $connectionParameters = $this->getConnectionParameters();
        
        // initialize the entity manager
        $entityManager = EntityManager::create($connectionParameters, $metadataConfiguration);
        
        // set the entity manager
        $this->setEntityManager($entityManager);
        
        // return the instance itself
        return $this;
    }
    
    /**
     * Returns the application name (that has to be the class namespace, 
     * e. g. TechDivision\Example).
     * 
     * @return string The application name
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Set's the path to the doctrine entities.
     * 
     * @param string $pathToEntities The path to the doctrine entities
     * @return \TechDivision\PersistenceContainer\Application The application instance
     */
    public function setPathToEntities($pathToEntities) {
        $this->pathToEntities = $pathToEntities;
        return $this;
    }
    
    /**
     * Return's the path to the doctrine entities.
     * 
     * @return string The path to the doctrine entities
     */
    public function getPathToEntities() {
        return $this->pathToEntities;
    }
    
    /**
     * Set's the database connection parameters.
     * 
     * @param array $connectionParameters The database connection parameters
     * @return \TechDivision\PersistenceContainer\Application The application instance
     */
    public function setConnectionParameters(array $connectionParameters) {
        $this->connectionParameters = $connectionParameters;
        return $this;
    }
    
    /**
     * Return's the database connection parameters.
     * 
     * @return array The database connection parameters
     */
    public function getConnectionParameters() {
        return $this->connectionParameters;
    }
    
    /**
     * Sets the applications entity manager instance.
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager The entity manager instance
     * @return \TechDivision\PersistenceContainer\Application The application instance
     */
    public function setEntityManager(ObjectManager $entityManager) {
        $this->entityManager = $entityManager;
        return $this;
    }
    
    /**
     * Return the entity manager instance.
     * 
     * @return \Doctrine\Common\Persistence\ObjectManager The entity manager instance
     */
    public function getEntityManager() {
        return $this->entityManager;
    }
    
}