<?php

namespace TechDivision\MessagingServiceClient;

/**
 * @package	mqclient
 * @author	wagnert <tw@struts4php.org>
 * @version $Revision: 1.2 $ $Date: 2008-10-17 09:44:23 $
 * @copyright struts4php.org
 * @link www.struts4php.org
 */
class Queue {

    /**
     * The queue name to use.
     * @var string
     */
    private $_name = null;

    /**
     * Initializes the queue with the name to use.
     * 
     * @param String $name Holds the queue name to use
     * @return void
     */
    private function __construct(String $name) {
        $this->_name = $name;
    }

    /**
     * Returns the queue name.
     * 
     * @return string The queue name
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Initializes and returns a new Queue instance.
     * 
     * @param String Holds the queue name to use
     * @return Queue The instance
     */
    public static function createQueue(String $name) {
        return new Queue($name);
    }

}