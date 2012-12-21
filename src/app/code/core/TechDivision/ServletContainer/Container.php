<?php

namespace TechDivision\ServletContainer;

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\Socket\Client;

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
    public function __construct($workerNumber = 1, $address = '0.0.0.0', $port = 8586) {

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
        // $this->gcEnable()->deploy();

        // create the worker instances
        for ($i = 0; $i < $this->getWorkerNumber(); $i++) {
            $this->workers[$i] = new RequestHandler($this);
            $this->workers[$i]->start();
        }
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