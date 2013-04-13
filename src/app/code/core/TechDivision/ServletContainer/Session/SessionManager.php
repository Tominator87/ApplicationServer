<?php
/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 07.04.13
 * Time: 00:09
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\ServletContainer\Session;


use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Http\HttpServletRequest;

interface SessionManager {

    /**
     * Tries to find a session for the given request. If no session
     * is found, a new one is created and assigned to the request.
     *
     * @param ServletRequest $request
     * @return ServletSession
     */
    public function getSessionForRequest(ServletRequest $request);

}