<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="rule")
 */
class Rule {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $ruleId;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $roleIdFk;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $assertionIdFk;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $resource;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $privileges;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $permission;

    /**
     * Returns the value of the class member ruleId.
     *
     * @return Integer Holds the value of the class member ruleId
     */
    public function getRuleId() {
        return $this->ruleId;
    }

    /**
     * Sets the value for the class member ruleId.
     *
     * @param Integer $Integer Holds the value for the class member ruleId
     */
    public function setRuleId($ruleId) {
        $this->ruleId = $ruleId;
    }

    /**
     * Returns the value of the class member roleIdFk.
     *
     * @return Integer Holds the value of the class member roleIdFk
     */
    public function getRoleIdFk() {
        return $this->roleIdFk;
    }

    /**
     * Sets the value for the class member roleIdFk.
     *
     * @param Integer $Integer Holds the value for the class member roleIdFk
     */
    public function setRoleIdFk($roleIdFk) {
        $this->roleIdFk = $roleIdFk;
    }

    /**
     * Returns the value of the class member assertionIdFk.
     *
     * @return Integer Holds the value of the class member assertionIdFk
     */
    public function getAssertionIdFk() {
        return $this->assertionIdFk;
    }

    /**
     * Sets the value for the class member assertionIdFk.
     *
     * @param Integer $Integer Holds the value for the class member assertionIdFk
     */
    public function setAssertionIdFk($assertionIdFk = null) {
        $this->assertionIdFk = $assertionIdFk;
    }

    /**
     * Returns the value of the class member resource.
     *
     * @return String Holds the value of the class member resource
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * Sets the value for the class member resource.
     *
     * @param String $String Holds the value for the class member resource
     */
    public function setResource($resource) {
        $this->resource = $resource;
    }

    /**
     * Returns the value of the class member privileges.
     *
     * @return String Holds the value of the class member privileges
     */
    public function getPrivileges() {
        return $this->privileges;
    }

    /**
     * Sets the value for the class member privileges.
     *
     * @param String $String Holds the value for the class member privileges
     */
    public function setPrivileges($privileges = null) {
        $this->privileges = $privileges;
    }

    /**
     * Returns the value of the class member permission.
     *
     * @return String Holds the value of the class member permission
     */
    public function getPermission() {
        return $this->permission;
    }

    /**
     * Sets the value for the class member permission.
     *
     * @param String $String Holds the value for the class member permission
     */
    public function setPermission($permission) {
        $this->permission = $permission;
    }

}