<?php

namespace TechDivision\Example\Servlets;

/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 05.10.12
 * Time: 15:40
 * To change this template use File | Settings | File Templates.
 */

use TechDivision\ServletContainer\Http\HttpServletRequest;
use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Servlets\HttpServlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Interfaces\ServletResponse;

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\ServletContainer\Session\ServletSession;

class SessionExampleServlet extends HttpServlet implements Servlet {

    /**
     * @param ServletRequest $req
     * @param ServletResponse $res
     */
    public function doGet(ServletRequest $req, ServletResponse $res) {

        /** @var $req HttpServletRequest */
        error_log(__METHOD__);
        error_log(var_export($req->getSession(),true));

    }

}
