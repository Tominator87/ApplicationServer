<?php

namespace TechDivision\Example\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\ServletContainer\Servlets\HttpServlet;

use TechDivision_Lang_String as String;

class HelloWorldServlet extends HttpServlet implements Servlet
{
    /**
     * @param ServletConfig $config
     * @return mixed|void
     */
    public function init(ServletConfig $config = null)
    {

    }

    /**
     * @param Request $req
     * @param Response $res
     */
    public function doGet(Request $req, Response $res)
    {
        $res->setContent(
            new String(
                "<!DOCTYPE html><html><head></head><body><h1>Hello World!</h1></body></html>"
            )
        );
    }

}