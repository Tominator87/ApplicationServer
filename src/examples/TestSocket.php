<?php

declare(ticks = 1) {
    
    class Client extends \Worker {
        
        public function __construct() {
            error_log(__METHOD__ . ':' . __LINE__);
        }
        
        public function run() {
            error_log(__METHOD__ . ':' . __LINE__);
        }
    }
    
    class Request extends \Stackable {
        
        protected $client;
        
        public function __construct($client) {
            
            $this->client = $client;
        }
    
        /**
         * Method that is executed, when a fatal error occurs.
         *
         * @return void
         */
        public function fatalErrorShutdown() {
            
            if (is_resource($this->client)) {
                error_log("Now close socket");
                socket_close($this->client);
            }
            
            error_log(__METHOD__ . ':' . __LINE__);
        }
        
        public function run() {
            
            register_shutdown_function(array($this, 'fatalErrorShutdown'));
            
    		$client = $this->client;
            
            if ($client) {
            
    			$header = 0;
    			
    			while (($chars = socket_read($client, 1024, PHP_NORMAL_READ))) {
    				$head[$header] = trim($chars);
    				if ($header>0) {
    					if (!$head[$header] && !$head[$header-1])
    						break;
    				}
    				$header++;
    			}
    			
    			foreach ($head as $header) {
    				if ($header) {
    					$headers[] = $header;	
    				}
    			}
    
    			$response = array(	
    				"head" => array(
    					"HTTP/1.0 200 OK",
    					"Content-Type: text/html"
    				), 
    				"body" => array()
    			);
    			
    			$this->doSomeShit();
    
    			socket_getpeername($client, $address, $port);
    
    			$response["body"][]="<html>";
    			$response["body"][]="<head>";
    			$response["body"][]="<title>Multithread Sockets PHP ({$address}:{$port})</title>";
    			$response["body"][]="</head>";
    			$response["body"][]="<body>";
    			$response["body"][]="<pre>";
    			foreach($headers as $header)
    				$response["body"][]="{$header}";
    			$response["body"][]="</pre>";
    			$response["body"][]="</body>";
    			$response["body"][]="</html>";
    			$response["body"] = implode("\r\n", $response["body"]);
    			$response["head"][] = sprintf("Content-Length: %d", strlen($response["body"]));
    			$response["head"] = implode("\r\n", $response["head"]);
    
    			socket_write($client, $response["head"]);
    			socket_write($client, "\r\n\r\n");
    			socket_write($client, $response["body"]);
    
    			socket_close($client);
    		}
        }
    }
    
    class TestContainer {
        
        protected $workerNumbers = 1;
        
        protected $workers;
        
        protected $server;
        
        public function __construct($server) {
            
            $this->server = $server;
            
            $this->workers = array();
        }
        
        public function start() {
            
            try {
            
                if (($socket = @socket_create_listen(8585)) === false) {
                    throw new \Exception(socket_last_error());
                }
                
                if (@socket_set_nonblock($socket) === false) {
                    throw new \Exception(socket_last_error($socket));
                }
                
                while (true) {
                    
                    if ($this->server->shutdown) {
                        
                        break;
                    }
                    
                    if ($client = @socket_accept($socket)) {
                        
                        $this->getRandomWorker()->stack($requests[] = new Request($client));
                    }
                    
                    usleep(500);
                }
                        
                error_log("Stop infinite loop");
                
                if (is_resource($socket)) {
                    socket_close($socket);
                }
                
                $this->shutdown();
                
            } catch (\Exception $e) {
                
                socket_close($socket);
                
                error_log($e->__toString());
            }
        }
        
        public function getRandomWorker() {


            $i = rand(0, $this->workerNumbers - 1);
            
            if (array_key_exists($i, $this->workers) === false) {
                
                $this->workers[$i] = new Client();
                $this->workers[$i]->start();
                
                error_log("Successfully created new worker " . $this->workers[$i]->getThreadId());
                
            } else {
                
                if ($this->workers[$i]->isWorking()) {
                    
                    $this->workerNumbers++;
                    
                    error_log("Raise worker number to {$this->workerNumbers}");
                    
                    return $this->getRandomWorker();
                }
            }
            
            return $this->workers[$i];
        }
        
        public function shutdown() {

            error_log("Found " . sizeof($this->workers) . " worker");
            
            // iterate over all threads and stop each of them
            foreach ($this->workers as $worker) {
            
                if ($worker->isWorking() === false) {
                    
                    $worker->shutdown();
            
                    error_log("Successfully shutdown worker " . $worker->getThreadId());
                }
            }
        }
    }
    
    class TestServer extends \Thread {
        
        protected $container;
        
        protected $shutdown = false;
        
        public function __construct() {
            
            // catch Ctrl+C, kill and SIGTERM (rollback)
            pcntl_signal(SIGTERM, array($this, 'sigintShutdown'));
            pcntl_signal(SIGINT, array($this, 'sigintShutdown'));
            
            $this->container = new TestContainer($this);
        }
        
        public function run() {
            $this->container->start();
        }
        
        public function stop() {
            
            error_log(__METHOD__ . ':' . __LINE__);
            
            $this->shutdown = true;
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
            error_log(__METHOD__ . ':' . __LINE__);
            $this->stop();
        }
    }
    
    
    $server = new TestServer();
    $server->start();
    
    while ($server->isRunning()) {
        sleep(1);
    }
    
    error_log("Successfully shutdown server");
    
}