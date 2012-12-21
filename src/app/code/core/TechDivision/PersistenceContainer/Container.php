<?php

/**
 * License: GNU General Public License
 *
 * Copyright (c) 2009 TechDivision GmbH.  All rights reserved.
 * Note: Original work copyright to respective authors
 *
 * This file is part of TechDivision GmbH - Connect.
 *
 * TechDivision_Lang is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * TechDivision_Lang is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 * USA.
 *
 * @package TechDivision\ApplicationServer
 */

namespace TechDivision\PersistenceContainer;

use TechDivision\Socket\Client;
use TechDivision\PersistenceContainer\Request;
use TechDivision\PersistenceContainer\RequestHandler;
use TechDivision\PersistenceContainer\Application;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

class Container extends Client implements ContainerInterface {
    
    /**
     * The number of parallel workers to handle client connections.
     * @var integer
     */
    protected $workerNumber = 1;

    /**
     * Array with the worker instances.
     * @var array
     */
    protected $workers = array();

    /**
     * Array with deployed applications.
     * @var array
     */
    protected $applications = array();

    /**
     * Array for the incoming requests.
     * @var array
     */
    protected $work = array();

    /**
     * Initializes the server instance with the storage.
     * 
     * @param integer $workerNumber Number of workers to start initially
     * @param string $address The container's IP address to listen to for incoming remote method calls
     * @param integer $port The container's port to listen to
     * @return void
     */
    public function __construct($workerNumber = 1, $address = '0.0.0.0', $port = 8585) {

        // pass address and port to the server
        parent::__construct($address, $port);
        
        // set the number of workers to start
        $this->setWorkerNumber($workerNumber);

        // catch Fatal Error (Rollback)
        register_shutdown_function(array($this, 'fatalErrorShutdown'));

        // catch Ctrl+C, kill and SIGTERM (rollback)
        pcntl_signal(SIGTERM, array($this, 'sigintShutdown'));
        pcntl_signal(SIGINT, array($this, 'sigintShutdown'));

        // enable garbage collector and deploy applications
        $this->gcEnable()->deploy();

        // create the worker instances
        for ($i = 0; $i < $this->getWorkerNumber(); $i++) {
            $this->workers[$i] = new RequestHandler($this);
            $this->workers[$i]->start();
        }
    }

    /**
     * Returns an array with available applications.
     * 
     * @return \TechDivision\Server The server instance
     * @todo Implement real deployment here
     */
    public function deploy() {

        // create the recursive directory iterator
        $di = new \RecursiveDirectoryIterator(getcwd() . '/app/code/local');
        
        // create the recursive iterator
        $it = new \RecursiveIteratorIterator($di);

        // iterate over the directory recursively and look for configurations
        while ($it->valid()) {

            // check if file or subdirectory has been found
            if (!$it->isDot()) {

                // if a configuration file was found
                if (basename($it->getSubPathName()) == 'appserver.xml') {

                    // initialize the SimpleXMLElement with the content of pointcut XML file
                    $sxe = new \SimpleXMLElement(file_get_contents($it->getSubPathName(), true));

                    // iterate over the found nodes
                    foreach ($sxe->xpath('/appserver/applications/application') as $application) {

                        // load the application name and the path to the entities
                        $name = (string) $application->name;
                        $pathToEntities = (string) $application->pathToEntities;

                        // load the database connection information
                        foreach ($application->children() as $database) {
                            $connectionParameters = array(
                                'driver' => (string) $database->driver,
                                'user' => (string) $database->user,
                                'password' => (string) $database->password,
                                'dbname' => (string) $database->databaseName,
                            );
                        }

                        // initialize the application instance
                        $application = new Application($name);
                        $application->setConnectionParameters($connectionParameters);
                        $application->setPathToEntities(array($pathToEntities));

                        // add the application to the available applications
                        $this->applications[$application->getName()] = $application;
                    }
                }
            }
            // proceed with the next folder
            $it->next();
        }

        // return the server instance
        return $this;
    }

    /**
     * Cleanup if process has been killed unexpectedly.
     * 
     * @return void
     */
    public function shutdown() {

        // close the main socket
        $this->close();

        // shutdown the workers
        foreach ($this->workers as $worker) {
            $worker->shutdown();
        }

        exit();
    }

    /**
     * Method that is executed, when a fatal error occurs.
     *
     * @return void
     */
    public function fatalErrorShutdown() {
        $lastError = error_get_last();
        if (!is_null($lastError) && $lastError['type'] === E_ERROR) {
            $this->shutdown();
        }
    }

    /**
     * Method, that is executed, if script has been killed by:
     * 
     * SIGINT: Ctrl+C
     * SIGTERM: kill
     *
     * @param int $signal
     */
    public function sigintShutdown($signal) {
        if ($signal === SIGINT || $signal === SIGTERM) {
            $this->shutdown();
        }
    }

    /**
     * Main method that starts the server.
     * 
     * @return void
     */
    public function start() {

        // prepare the main socket and listen
        $this->create()
             ->setBlock()
             ->setReuseAddr()
             ->setReceiveTimeout()
             ->bind()
             ->listen();

        // start the ifinite loop and listen to clients
        while (true) {

            try {

                // prepare array of readable client sockets
                $read = array($this->resource);

                // prepare the array for write/except sockets
                $write = $except = array();

                // select a socket to read from
                $this->select($read, $write, $except);

                // if ready contains the master socket, then a new connection has come in
                if (in_array($this->resource, $read)) {

                    // initialize the buffer
                    $buffer = '';

                    // load the character for line ending
                    $newLine = $this->getNewLine();

                    // get the client socket (in blocking mode)
                    $client = $this->accept();

                    // read one line (till EOL) from client socket
                    while ($buffer .= $client->read($this->getLineLength())) {
                        if (substr($buffer, -1) === $newLine) {
                            $line = rtrim($buffer, $newLine);
                            break;
                        }
                    }

                    // close the client socket if no more data will be transmitted
                    if ($line == null) {
                        $client->close();
                    } else {
                        // pass the line to the worker instance and process it
                        $this->getRandomWorker()->stack($this->work[] = new Request($line));
                    }
                    
                    // if garbage collection is enabled, force collection of cycles immediately
                    if ($this->gcEnabled()) {
                        error_log("Collected {$this->gc()} cycles");
                    }
                }
            } catch (Exception $e) {
                error_log($e->__toString());
            }
        }
    }
    
    /**
     * Forces collection of any existing garbage cycles.
     * 
     * @return integer The number of collected cycles
     * @link http://php.net/manual/en/features.gc.collecting-cycles.php
     */
    public function gc() {
        return gc_collect_cycles();
    }
    
    /**
     * Returns TRUE if the PHP internal garbage collection is enabled.
     * 
     * @return boolean TRUE if the PHP internal garbage collection is enabled
     * @link http://php.net/manual/en/function.gc-enabled.php
     */
    public function gcEnabled() {
        return gc_enabled();
    }
    
    /**
     * Enables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-enable.php
     */
    public function gcEnable() {
        gc_enable();
        return $this;
    }
    
    /**
     * Disables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-disable.php
     */
    public function gcDisable() {
        gc_disable();
        return $this;
    }

    /**
     * Set's the maximum number of workers to start.
     * 
     * @param integer $workerNumber The maximum number of worker's to start
     * @return \TechDivision\PersistenceContainer\Container The container instance
     */
    public function setWorkerNumber($workerNumber) {
        $this->workerNumber = $workerNumber;
        return $this;
    }
    
    /**
     * Return's the maximum number of workers to start.
     * 
     * @return integer The maximum number of worker's to start
     */
    public function getWorkerNumber() {
        return $this->workerNumber;
    }
    
    /**
     * Returns an array with the deployed applications.
     * 
     * @return array The array with applications
     */
    public function getApplications() {
        return $this->applications;
    }

    /**
     * Returns a random worker.
     * 
     * @return \Worker The random worker instance
     */
    public function getRandomWorker() {
        return $this->workers[rand(0, $this->getWorkerNumber() - 1)];
    }

}