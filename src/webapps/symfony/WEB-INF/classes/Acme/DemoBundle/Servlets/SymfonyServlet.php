<?php

namespace Acme\DemoBundle\Servlets;

use Symfony\Component\HttpFoundation\Request;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Interfaces\ServletResponse;

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;

class SymfonyServlet extends HttpServlet implements Servlet {

    public function __construct() {
    }

    /**
     * @param TechDivision\ServletContainer\ServletConfig $config
     * @return mixed|void
     */
    public function init(ServletConfig $config = null) {
    }

    /**
     * @param ServletRequest $req
     * @param ServletResponse $res
     */
    public function doGet(ServletRequest $req, ServletResponse $res) {

        // If you don't want to setup permissions the proper way, just uncomment the following PHP line
        // read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
        // umask(0000);

        // This check prevents access to debug front controllers that are deployed by accident to production servers.
        // Feel free to remove this, extend it, or make something more sophisticated.
        /*
        if (isset($_SERVER['HTTP_CLIENT_IP'])
            || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
        ) {
            header('HTTP/1.0 403 Forbidden');
            exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
        }
        */

        $loader = require_once 'webapps/symfony/bootstrap.php.cache';
        require_once 'webapps/symfony/WEB-INF/classes/AppKernel.php';

        $kernel = new \AppKernel('dev', true);
        $kernel->loadClassCache();
        Request::enableHttpMethodParameterOverride();
        // $request = Request::createFromGlobals();

        $request = new Request($req->getParameterMap());
        $response = $kernel->handle($request);

        // $response->send();
        $kernel->terminate($request, $response);

        $res->setContent($response->getContent());
    }
}