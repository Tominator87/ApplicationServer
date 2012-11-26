<?php
    
namespace TechDivision\PersistenceContainer;

use TechDivision\ApplicationServer\InitialContext;

class Application {
    
    /**
     * The initial context instance
     * @var InitialContext
     */
    protected $_initialContext = null;
    
    protected $_entityManager = null;

    protected $_name = null;
    
    public function __construct($name) {
        $this->_name = $name;
    }
    
    public function deploy() {
        error_log('Do something here');
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function setInitialContext($initialContext) {
        $this->_initialContext = $initialContext;
    }
    
    public function getInitialContext() {
        return $this->_initialContext;
    }
    
    public function setEntityManager($entityManager) {
        $this->_entityManager = $entityManager;
    }
    
    public function getEntityManager() {
        return $this->_entityManager;
    }
    
}