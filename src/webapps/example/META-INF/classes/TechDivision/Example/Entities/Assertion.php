<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="assertion")
 **/
class Assertion {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $assertionId;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $type;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $includeFile;

    /**
     * Returns the value of the class member assertionId.
     *
     * @return integer Holds the value of the class member assertionId
     */
    public function getAssertionId() {
        return $this->assertionId;
    }

    /**
     * Sets the value for the class member assertionId.
     *
     * @param integer $assertionId Holds the value for the class member assertionId
     */
    public function setAssertionId($assertionId) {
        $this->assertionId = $assertionId;
    }

    /**
     * Returns the value of the class member type.
     *
     * @return string Holds the value of the class member type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Sets the value for the class member type.
     *
     * @param string $type Holds the value for the class member type
     */
    public function setType($type) {
        $this->type = $type;
    }

    /**
     * Returns the value of the class member includeFile.
     *
     * @return string Holds the value of the class member includeFile
     */
    public function getIncludeFile() {
        return $this->includeFile;
    }

    /**
     * Sets the value for the class member includeFile.
     *
     * @param string $includeFile Holds the value for the class member includeFile
     */
    public function setIncludeFile($includeFile) {
        $this->includeFile = $includeFile;
    }

}