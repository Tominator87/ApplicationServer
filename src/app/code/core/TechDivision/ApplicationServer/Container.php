<?php

namespace TechDivision\ApplicationServer;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
// use Doctrine\ORM\Tools\SchemaTool;

class Container {

    protected static $_instance = null;
    
    protected $_storage = array();
    
    protected $_entityManager = null; 

    public function __construct() {

        $dbParams = array(
            'path' => '/tmp/appserver.db', 
            'driver' => 'pdo_sqlite'
        );
        
        $path = array('TechDivision/Example/Entities');
        
        $config = Setup::createAnnotationMetadataConfiguration($path, true);
        $this->_entityManager = EntityManager::create($dbParams, $config);
        
        /*
        try {
            $tool = new SchemaTool($this->_entityManager);
            $classes = array(
                $this->_entityManager->getClassMetadata('TechDivision\Example\Entities\Sample')
            );
            $tool->createSchema($classes);
        } catch (\Exception $te) {
            error_log($te->__toString());
        }
        */
    }
    
    public function getEntityManager() {
        return $this->_entityManager;
    }

    public function lookup($id) {
        // maybe implement session lookup here
    }

    public static function singleton() {
        if (self::$_instance == null) {
            self::$_instance = new Container();
        }
        return self::$_instance;
    }

}