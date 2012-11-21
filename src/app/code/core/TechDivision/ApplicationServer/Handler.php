<?php

namespace TechDivision\ApplicationServer;

use Doctrine\Common\ClassLoader;

class Handler extends \Thread {

    protected $_name;
    
    protected $_maxClients = 20;

    public function __construct($name) {
        $this->_name = $name;
    }

    public function getName() {
        return $this->_name;
    }
    
    public function getMaxClients() {
        return $this->_maxClients;
    }

    public function run() {

        $classLoader = new ClassLoader();
        $classLoader->register();
        
        $container = Container::singleton();
        
        $maxClients = $this->getMaxClients();

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        socket_set_option($socket, SOL_TCP, TCP_NODELAY, 1);
        socket_set_option($socket, SOL_TCP, SO_KEEPALIVE, -1);
        
        socket_set_nonblock($socket);
        socket_bind($socket, '192.168.250.128', 8585);
        socket_listen($socket, $maxClients);

        $clients = array('0' => array('socket' => $socket));

        while (true) {
            
            $read[0] = $socket;
            
            for ($i = 1; $i < count($clients) + 1; $i++) {
                if (array_key_exists($i, $clients)) {
                    $read[$i + 1] = $clients[$i]['socket'];
                }
            }

            $ready = socket_select($read, $write = null, $except = null, 0);

            if (in_array($socket, $read)) {
                
                for ($i = 1; $i < $maxClients + 1; $i++) {
                    
                    if (!isset($clients[$i])) {
                        $clients[$i]['socket'] = socket_accept($socket);
                        socket_getpeername($clients[$i]['socket'], $ip);
                        $clients[$i]['ipaddy'] = $ip;
                        // error_log("New client $i connected: " . $clients[$i]['ipaddy']);
                        break;
                    } elseif ($i == $maxClients - 1) {
                        // error_log("To many Clients connected!");
                    }
                    
                    if ($ready < 1) {
                        continue;
                    }
                }
            }
            
            for ($i = 1; $i < $maxClients + 1; $i++) {
                
                if (array_key_exists($i, $clients) && in_array($clients[$i]['socket'], $read)) {
                    
                    $data = @socket_read($clients[$i]['socket'], 1024000, PHP_NORMAL_READ);
                    
                    if ($data === false) {
                        unset($clients[$i]);
                        // error_log("Client $i disconnected!");
                        continue;
                    }
                    
                    $serialized = rtrim($data, "\r\n\r\n");

                    $request = unserialize($serialized);    
                    
                    if ($request !== false) {
                        
                        list ($class, $method, $params) = $request;
                        $instance = new $class($container);
                        $response = call_user_func_array(array($instance, $method), $params);
                        $serialized = serialize($response);
                        socket_write($clients[$i]['socket'],  $serialized . "\n");
                    }
                }
            }
            
            usleep(100);
        }
    }

}