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

use TechDivision\ApplicationServer\InitialContext;
use Doctrine\Common\Persistence\ObjectManager;

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
     * The initial context instance.
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $_initialContext = null;
    
    /**
     * The entity manager that handles the entity beans.
     * @var \Doctrine\Common\Persistence\ObjectManager 
     */
    protected $_entityManager = null;

    /**
     * The application name.
     * @var string
     */
    protected $_name = null;
    
    /**
     * Passes the application name That has to be the class namespace.
     * 
     * @param type $name The application name
     */
    public function __construct($name) {
        $this->_name = $name;
    }
    
    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     * 
     * @return void
     */
    public function deploy() {
        // still to implement
    }
    
    /**
     * Returns the application name (that has to be the class namespace, 
     * e. g. TechDivision\Example).
     * 
     * @return string The application name
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Sets the initial context instance.
     * 
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     */
    public function setInitialContext(InitialContext $initialContext) {
        $this->_initialContext = $initialContext;
    }
    
    /**
     * Returns the initial context instance.
     * 
     * @return \TechDivision\ApplicationServer\InitialContext The initial context
     */
    public function getInitialContext() {
        return $this->_initialContext;
    }
    
    /**
     * Sets the applications entity manager instance.
     * 
     * @param \Doctrine\Common\Persistence\ObjectManager $entityManager The entity manager instance
     */
    public function setEntityManager(ObjectManager $entityManager) {
        $this->_entityManager = $entityManager;
    }
    
    /**
     * Return the entity manager instance.
     * 
     * @return \Doctrine\Common\Persistence\ObjectManager The entity manager instance
     */
    public function getEntityManager() {
        return $this->_entityManager;
    }
    
}