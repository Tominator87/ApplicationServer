<?php

/**
 * TechDivision\ServletContainer\HttpServlet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ServletContainer\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletResponse;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Service\Locator\StaticResourceLocator;
use TechDivision\ServletContainer\Exceptions\MethodNotImplementedException;
use TechDivision\ServletContainer\Exceptions\PermissionDeniedException;
use TechDivision\ServletContainer\Exceptions\ServletException;
use TechDivision\ServletContainer\Exceptions\IOException;

/**
 * Abstract Http servlet implementation.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Markus Stockbauer <ms@techdivision.com>
 * @author      Tim Wagner <tw@techdivision.com>
 */
class PhpServlet extends HttpServlet implements Servlet {

    /**
     * Tries to load the requested file and adds the content to the response.
     *
     * @param \TechDivision\ServletContainer\Interfaces\ServletRequest $req The servlet request
     * @param \TechDivision\ServletContainer\Interfaces\ServletResponse $res The servlet response
     * @throws \TechDivision\ServletContainer\Exceptions\PermissionDeniedException Is thrown if the request tries to execute a PHP file
     * @return void
     */
    public function doGet(ServletRequest $req, ServletResponse $res) {

        // instanciate the resource locator
        $locator = new StaticResourceLocator();

        // let the locator retrieve the file
        $file = $locator->locate($req);

        // start output buffering
        ob_start();

        /**
         * IMPORTANT: can not use "require_once" here, otherwise the worker will never
         * run the same php file twice.
         *
         * @thanks Prof. Dr. Dr. ing. Spexx
         */
        require $file->getRealPath();

        // store the file's contents in the response
        $res->setContent(ob_get_clean());
    }

    /**
     * Tries to load the requested file and adds the content to the response.
     *
     * @param \TechDivision\ServletContainer\Interfaces\ServletRequest $req The servlet request
     * @param \TechDivision\ServletContainer\Interfaces\ServletResponse $res The servlet response
     * @throws \TechDivision\ServletContainer\Exceptions\MethodNotImplementedException
     * @return void
     */
    public function doPost(ServletRequest $req, ServletResponse $res) {
        throw new MethodNotImplementedException(sprintf('Method %s is not implemented in this servlet.', __METHOD__));
    }

    /**
     * @param Request $req
     * @param Response $res
     * @throws ServletException
     * @throws IOException
     * @return mixed
     */
    public function service(ServletRequest $req, ServletResponse $res) {
        $this->doGet($req, $res);
    }
}