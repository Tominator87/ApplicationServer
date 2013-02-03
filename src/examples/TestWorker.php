<?php

declare(ticks = 1);

class TestWorker extends \Worker {
	
	public function run() {
		error_log(__METHOD__ . ':' . __LINE__);		
	}
}

class TestRequest extends \Stackable {

    /**
     *  The number of bytes to send/receive.
     * @var integer
     */
    protected $lineLength = 2048;

    /**
     * New line character.
     * @var string
     */
    protected $newLine = "\n";
	
	protected $client;
	
	public function __construct($client) {
		$this->client = $client;
	}
	
	public function run() {
		
		if ($this->worker) {
			
			$client = $this->client;
            
	        // initialize the buffer
	        $buffer = '';
	
	        // set the new line character
	        $newLine = $this->newLine;
	        
	        // read a chunk from the socket
	        while ($buffer .= socket_read($client, $this->lineLength, PHP_BINARY_READ)) {
	            // check if a new line character was found
	            if (substr($buffer, -1) === $newLine) {
	                // if yes, trim and return the data
	                return rtrim($buffer, $newLine);
	            }
	        }

			error_log(__METHOD__ . ':' . __LINE__);
			
			$response = array();
			
			socket_write($client, serialize($response) . "\n");
	        
			/* 
			 * read from the socket, process the request and send data back to the client
			 */
			socket_close($client);
		}
	}
}

class TestReceiver {
	
	protected $work;
	
	protected $workers;
	
	public function __construct($container) {
		$this->container = $container;
		$this->work = array();
		$this->workers = array();
	}
	
	public function start() {

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($socket, '127.0.0.1', 8585);
		socket_listen($socket);
		socket_set_nonblock($socket);
		
		while (true) {
			
			try {
				
				if (apc_exists('system_shutdown') && apc_fetch('system_shutdown') === true) {
					break;
				}
				
				if ($client = @socket_accept($socket)) {
				
					$request = new TestRequest($client);
					
					$worker = $this->getRandomWorker();
					
					$worker->stack($this->work[] = $request);
				}
				
			} catch(\Exception $e) {
				error_log($e->__toString());
			}
			
			usleep(300);
		}
		
		socket_close($socket);
		
		$socket = array();

		$this->shutdown();
	}
	
	public function shutdown() {

		error_log("Found " . ($work = sizeof($this->work))  . " work");
		
		$this->work = array();
		
		error_log("Found " . ($workers = sizeof($this->workers))  . " workers");
		
		foreach ($this->workers as $worker) {
			$worker->shutdown();
		}
		
		$this->workers = array();

		error_log("Successfully shutdown $workers worker");
	}
	
	public function getRandomWorker() {
		
		$i = rand(0, 3);
		
		if (array_key_exists($i, $this->workers) === false) {
			$worker = new TestWorker();
			$worker->start();
			$this->workers[$i] = $worker;
		}
		
		return $this->workers[$i];
	}
}

class TestContainer {
	
	protected $receiver;
	
	public function start() {
		$this->receiver = new TestReceiver($this);
		$this->receiver->start();
	}
}

class TestThread extends \Thread {
	
	protected $container;
	
	public function run() {
		
		$this->container = new TestContainer();
		$this->container->start();
		
		$this->notify();
	}
}

class Server {
	
	protected $threads = array();
	
	public function start() {
		
		for ($i = 0; $i < 1; $i++) {
			$this->threads[$i] = new TestThread();
			$this->threads[$i]->start(); 
		}
		
		while (true) {
			sleep(1);
		}
	}

    public function __construct() {

        // catch fatal error (rollback)
        register_shutdown_function(array($this, 'fatalErrorShutdown'));

        // catch Ctrl+C, kill and SIGTERM (rollback)
        pcntl_signal(SIGTERM, array($this, 'sigintShutdown'));
        pcntl_signal(SIGINT, array($this, 'sigintShutdown'));
    	
    	apc_store('system_shutdown', false);
    }

    public function shutdown() {
    	
    	apc_store('system_shutdown', true);
    	
    	sleep(1);
    	
    	for ($i = 0; $i < 1; $i++) {
    		
    		error_log("Now try to shutdown thread " . $this->threads[$i]->getThreadId());
    		
    		$this->threads[$i]->join();
    		
    		error_log("Successfully shutdown thread " . $this->threads[$i]->getThreadId());
    	}
    	
        die ("System shutdown complete\n");
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
}

$server = new Server();
$server->start();