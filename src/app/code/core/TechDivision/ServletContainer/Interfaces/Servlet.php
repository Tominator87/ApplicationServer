<?php

namespace TechDivision\ServletContainer\Interfaces;

use TechDivision\ServletContainer\Interfaces\ServletConfig;
use TechDivision\ServletContainer\Interfaces\Request;
use TechDivision\ServletContainer\Interfaces\Response;

interface Servlet
{
    /**
     * @abstract
     * @param ServletConfig $config
     * @throws ServletException;
     * @return mixed
     */
    public function init(ServletConfig $config = null);

    /**
     * @abstract
     * @return ServletConfig
     */
    public function getServletConfig();

    /**
     * @abstract
     * @param ServletRequest $req
     * @param ServletResponse $res
     * @throws ServletException
     * @throws IOException
     * @return mixed
     */
    public function service(Request $req, Response $res);

    /**
     * @abstract
     * @return mixed
     */
    public function getServletInfo();

    /**
     * @abstract
     * @return mixed
     */
    public function destroy();

}
