<?php

namespace TechDivision\Example\Services;

use TechDivision\Example\Entities\Sample;
use TechDivision\ApplicationServer\InitialContext;
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
        
        // load the entity manager
        $entityManager = $this->getApplication()->getEntityManager();
        
        // check if a detached entity has been passed
        if ($entity->getSampleId()) {
            
            // if yes, merge the state of the detached entity into the unity of work
            $merged = $entityManager->merge($entity);

            // update the entity
            $entityManager->persist($merged);
        } else {

            // else create a new entity
            $entityManager->persist($entity);
        }
        
        // flush the entity manager
        $entityManager->flush();
    }
    
    public function delete($id) {
        $entityManager = $this->getApplication()->getEntityManager();
        $entityManager->remove($this->load($id));
        $entityManager->flush();
        return $this->findAll();
    }

    public function findAll() {
        $entityManager = $this->getApplication()->getEntityManager();
        $repository = $entityManager->getRepository('TechDivision\Example\Entities\Sample');
        return $repository->findAll();
    }
    
    public function changeWorker($workerNumber) {
        $key = 'TechDivision\ApplicationServer\AbstractReceiver';
        $configuration = InitialContext::get()->getAttribute($key);
        $configuration->setWorkerNumber($workerNumber);
        return InitialContext::get()->setAttribute($key, $configuration);
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