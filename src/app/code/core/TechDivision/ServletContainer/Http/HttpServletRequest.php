<?php

/**
 * TechDivision\ServletContainer\Http\HttpServletRequest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Http;

use TechDivision\ServletContainer\Http\Request;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\PersistenceContainerClient\Interfaces\Session;
use TechDivision\ServletContainer\Session\PersistentSessionManager;
use TechDivision\ServletContainer\Session\SessionManager;

/**
 * The Http servlet request implementation.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Markus Stockbauer <ms@techdivision.com>
 */
class HttpServletRequest implements ServletRequest {

    /**
     * @var String
     */
    protected $inputStream;

    /**
     * @var \TechDivision\ServletContainer\Http\Request
     */
    protected $request;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var SessionManager
     */
    protected $sessionManager;

    /**
     * @param string $inputStream
     */
    private function __construct($inputStream) {
        $this->setInputStream($inputStream);
        $this->setRequest(Request::parse((string) $inputStream));
        $this->sessionManager = new PersistentSessionManager();
    }

    /**
     * @param \TechDivision_Lang_String $inputStream
     * @return void
     */
    public function setInputStream($inputStream) {
        $this->inputStream = $inputStream;
    }

    /**
     * @return String
     */
    public function getInputStream() {
        return $this->inputStream;
    }

    /**
     * @param \TechDivision_Lang_String $inputStream
     * @return HttpServletRequest
     */
    public static function factory($inputStream) {
        return new HttpServletRequest($inputStream);
    }

    /**
     * @param \TechDivision\ServletContainer\Http\Request $request
     */
    public function setRequest($request) {
        $this->request = $request;
    }

    /**
     * @return \TechDivision\ServletContainer\Http\Request
     */
    public function getRequest() {
        return $this->request;
    }

    public function getRequestUrl() {
        return $this->getRequest()->getPathInfo();
    }

    /**
     * Returns the session for this request.
     *
     * @return Session
     */
    public function getSession() {

        if(is_null($this->session)) {
            $this->session = $this->sessionManager->getSessionForRequest($this);
        }

        return $this->session;
    }



}