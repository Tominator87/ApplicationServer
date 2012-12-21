<?php

namespace TechDivision\ServletContainer\Servlets;

use TechDivision\ServletContainer\Interfaces\Servlet;
use TechDivision\ServletContainer\Interfaces\Response;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Servlets\GenericServlet;
use TechDivision\ServletContainer\Exceptions\MethodNotImplementedException;
use TechDivision\ServletContainer\Exceptions\ServletException;
use TechDivision\ServletContainer\Exceptions\IOException;

abstract class HttpServlet extends GenericServlet implements Servlet
{
    /**
     * @param Request $req
     * @param Response $res
     * @throws MethodNotImplementedException
     * @return void
     */
    public function doGet(Request $req, Response $res)
    {
        throw new MethodNotImplementedException(sprintf('Method %s is not implemented in this servlet.', __METHOD__));
    }

    /**
     * @param Request $req
     * @param Response $res
     * @throws MethodNotImplementedException
     * @return void
     */
    public function doPost(Request $req, Response $res)
    {
        throw new MethodNotImplementedException(sprintf('Method %s is not implemented in this servlet.', __METHOD__));
    }

    /**
     * @param Request $req
     * @param Response $res
     * @throws ServletException
     * @throws IOException
     * @return mixed
     */
    public function service(Request $req, Response $res)
    {
        $this->doGet($req, $res);
    }
}