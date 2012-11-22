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
 * @package TechDivision\ApplicationServerClient
 */

namespace TechDivision\ApplicationServerClient;

use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;

/**
 * Abstract base class of the Maps.
 *
 * @package TechDivision\ApplicationServerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class RemoteMethodCall implements RemoteMethod {
	
	/**
	 * The class name to invoke the method on.
	 * @var string
	 */
	protected $_className = null;
	
	/**
	 * The method name to invoke on the class
	 * @var string
	 */
	protected $_methodName = null;
	
	/**
	 * Parameters for the method.
	 * @var array
	 */
	protected $_parameters = array();
	
	/**
	 * The session ID to use for the method call.
	 * @var string
	 */
	protected $_sessionId = null;
	
	/**
	 * Initialize the instance with the necessary params.
	 * 
	 * @param string $className The class name to invoke the method on
	 * @param string $methodName The method name to invoke
	 * @param string $sessionId The session ID to use for the method call
	 * @return void
	 */
	public function __construct($className, $methodName, $sessionId) {
		$this->_className = $className;
		$this->_methodName = $methodName;
		$this->_sessionId = $sessionId;
	}
	
	/**
	 * Adds passed parameter to the array with the parameters.
	 * 
	 * @param string $key The parameter name
	 * @param mixed $value The parameter value
	 */
	public function addParameter($key, $value) {
		$this->_parameters[$key] = $value;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteMethod::getParameter()
	 */
	public function getParameter($key) {
		return $this->_parameters[$key];
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteMethod::getParameters()
	 */
	public function getParameters() {
		return $this->_parameters;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteMethod::getClassName()
	 */
	public function getClassName() {
		return $this->_className;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteMethod::getMethodName()
	 */
	public function getMethodName() {
		return $this->_methodName;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteMethod::getSessionId()
	 */
	public function getSessionId() {
		return $this->_sessionId;
	}
}