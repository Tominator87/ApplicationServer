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

use TechDivision\ApplicationServerClient\Interfaces\RemoteObject;
use TechDivision\ApplicationServerClient\Interfaces\RemoteMethod;
use TechDivision\ApplicationServerClient\Interfaces\Session;

/**
 * The proxy is used to create a new remote object of the 
 * class with the requested name.
 * 
 * namespace TechDivision\ApplicationServerClient;
 * 
 * use TechDivision\ApplicationServerClient\Proxy;
 * use TechDivision\ApplicationServerClient\Context\Connection\Factory;
 * 
 * $connection = Factory::createContextConnection();
 * $session = $connection->createContextSession();
 * $proxy = Proxy::create('TechDivision\ApplicationServerClient\Proxy\InitalContext');
 * $initalContext = $session->createProxy($proxy);
 *
 * $processor = $initialContext->lookup('Some\ProxyClass');
 *
 * @package TechDivision\ApplicationServerClient
 * @author Tim Wagner <t.wagner@techdivision.com>
 * @copyright TechDivision GmbH
 * @link http://www.techdivision.com
 * @license GPL
 */
class Proxy implements RemoteObject {
	
	/**
	 * Holds the ContextSession for this proxy.
	 * @var TechDivision\ApplicationServerClient\Interfaces\Session
	 */
	protected $_session = null;
	
	/**
	 * The class name to proxy.
	 * @var string
	 */
	protected $_className = null;
	
	/**
	 * Initializes the proxy with the class name to proxy.
	 * @param string $name
	 */
	public function __construct($className) {
		$this->_className = $className;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteObject::getClassName()
	 */
	public function getClassName() {
		return $this->_className;
	}
	
	/**
	 * Sets the session with the connection instance.
	 * 
	 * @param TechDivision\ApplicationServerClient\Interfaces\Session $session
	 * 		The session instance to use
	 */
	public function setSession(Session $session) {
		$this->_session = $session;
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see TechDivision\ApplicationServerClient\Interfaces\RemoteObject::getSession()
	 */
	public function getSession() {
	    return $this->_session;
	}
	
	/**
	 * Invokes the remote execution of the passed remote method.
	 * 
	 * @param string $method The remote method to call
	 * @param array $params The parameters for the method call
	 */
	public function __call($method, $params) {
		$methodCall = new RemoteMethodCall($this->getClassName(), $method, $this->getSession()->getSessionId());
        foreach ($params as $key => $value) {
            $methodCall->addParameter($key, $value);
        }
		return $this->__invoke($methodCall, $this->getSession());	
	}
	
	/**
	 * Invokes the remote execution of the passed remote method.
	 * 
	 * @param TechDivision\ApplicationServerClient\Interfaces\RemoteMethod $methodCall
	 * 		The remote method call instance
	 * @param TechDivision\ApplicationServerClient\Interfaces\Session $session
	 * 		The session with the connection instance to use
	 */
	public function __invoke(RemoteMethod $methodCall, Session $session) {
		return $this->setSession($session)->getSession()->send($methodCall);	
	}

	/**
	 * Factory method to create a new instance of the requested proxy implementation.
	 * 
	 * @param string $className The name of the class to create the proxy for
	 * @return TechDivision\ApplicationServerClient\Interfaces\RemoteObject 
	 * 		The proxy instance
	 */
	public static function create($className) {
		return new Proxy($className);
	}
}