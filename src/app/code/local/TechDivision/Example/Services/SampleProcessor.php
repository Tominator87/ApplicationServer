<?php

namespace TechDivision\Example\Services;

use TechDivision\Example\Entities\Sample;
use TechDivision\PersistenceContainer\Application;
use TechDivision\PersistenceContainer\Interfaces\Singleton;
use Doctrine\ORM\Tools\SchemaTool;

class SampleProcessor implements Singleton {

    public function __construct(Application $application) {
        $this->_application = $application;
    }

    public function getApplication() {
        return $this->_application;
    }

    public function load($id) {
        $entityManager = $this->getApplication()->getEntityManager();     
        return $entityManager->find('TechDivision\Example\Entities\Sample', $id);
    }

    public function persist(Sample $entity) {
        $entityManager = $this->getApplication()->getEntityManager();
        $entityManager->persist($entity);
        $entityManager->flush();
    }

    public function findAll() {
        $entityManager = $this->getApplication()->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\Sample');
        return $repository->findAll();
    }
    
    public function createSchema() {
        
        $entityManager = $this->getApplication()->getEntityManager();
        
        $tool = new SchemaTool($entityManager);

        $classes = array(
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Assertion'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Resource'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Role'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Rule'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\Sample'),
            $entityManager->getClassMetadata('TechDivision\Example\Entities\User')
        );
        
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }

}