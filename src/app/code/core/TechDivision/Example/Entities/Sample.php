<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="sample")
 **/
class Sample {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     **/
    private $id;

    /**
     * @Column(type="string")
     * @var string
     **/
    private $name;

    
    public function __construct() {
        error_log(__METHOD__ . ':' . __LINE__);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
    }
    
    public function getName() {
        return $this->name;
    }
}