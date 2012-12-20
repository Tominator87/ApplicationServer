<?php

namespace TechDivision\PersistenceContainer;

class Request extends \Stackable {
    
    /**
     * The serialized remote method call.
     * @var string
     */
    public $line;
    
    /**
     * Initializes the request with the serialized remote method data.
     * 
     * @param string $line The serialized remote method data 
     */
    public function __construct($line) {
        $this->line = $line;
    }
    
    /**
     * @see \Stackable::run()
     */
    public function run() {
        // check if a worker is available
        if ($this->worker) {
            // process the request
            $this->worker->processRequest($this->line);
            // notify the calling thread
            $this->notify();
        }
    }
}