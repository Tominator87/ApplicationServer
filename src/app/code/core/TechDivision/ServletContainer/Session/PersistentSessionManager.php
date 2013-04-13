<?php
/**
 * Created by JetBrains PhpStorm.
 * User: schboog
 * Date: 07.04.13
 * Time: 00:09
 * To change this template use File | Settings | File Templates.
 */

namespace TechDivision\ServletContainer\Session;

use TechDivision\PersistenceContainerClient\Context\Connection\Factory;
use TechDivision\PersistenceContainerClient\Context\ContextSession;
use TechDivision\ServletContainer\Interfaces\ServletRequest;
use TechDivision\ServletContainer\Http\HttpServletRequest;

class PersistentSessionManager implements SessionManager {

    const SESSION_NAME = 'PHPSESSID';

    /**
     * Tries to find a session for the given request. The session id
     * is searched in the cookie header of the request, and in the
     * request query string. If both values are present, the value
     * in the query string takes precedence. If no session id
     * is found, a new one is created and assigned to the request.
     *
     * @param ServletRequest $request
     * @return ServletSession
     */
    public function getSessionForRequest(ServletRequest $request)
    {
        /** @var $request HttpServletRequest */
        // @todo merge refactoring for headers getter by bcmzero
        $headers = $request->getRequest()->headers;
        $sessionId = null;

        // try to retrieve the session id from the cookies in request header
        if(isset($headers['cookie'])) {
            foreach(explode(';', $headers['cookie']) as $cookie) {
                list($name, $value) = explode('=', $cookie);
                if($name === self::SESSION_NAME) {
                    $sessionId = $value;
                }
            }
        }

        // try to retrieve the session id from the request query string
        // @todo merge refactoring for query string parameters getter by bcmzero
        $params = array();
        parse_str($request->getRequest()->query_string, $params);
        if(isset($params[self::SESSION_NAME])) {
            $sessionId = $params[self::SESSION_NAME];
        }

        // initialize a new session if none is present yet
        if(is_null($sessionId)) {
            // @todo make session id really unique over all requests
            $sessionId = uniqid();
        }

        // register the session id with php's standard logic
        session_id($sessionId);

        $connection = Factory::createContextConnection();
        /** @var $session ContextSession */
        $session = $connection->createContextSession();
        $context = $session->createInitialContext();

        /** @var $persistentSession ServletSession */
        $persistentSession = $context->lookup('\TechDivision\ServletContainer\Session\ServletSession');
        $persistentSession->setSessionId($sessionId);

        return $persistentSession;
    }

}