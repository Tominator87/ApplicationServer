<?php

namespace TechDivision\Example\Entities;

/**
 * @Entity @Table(name="role")
 */
class Role {

    /**
     * @Id @GeneratedValue @Column(type="integer")
     * @var int
     */
    protected $roleId;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $roleIdFk;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $userIdFk;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $name;

    /**
     * Returns the value of the class member roleId.
     *
     * @return integer Holds the value of the class member roleId
     */
    public function getRoleId() {
        return $this->roleId;
    }

    /**
     * Sets the value for the class member roleId.
     *
     * @param integer $roleId Holds the value for the class member roleId
     */
    public function setRoleId($roleId) {
        $this->roleId = $roleId;
    }

    /**
     * Returns the value of the class member roleIdFk.
     *
     * @return integer Holds the value of the class member roleIdFk
     */
    public function getRoleIdFk() {
        return $this->roleIdFk;
    }

    /**
     * Sets the value for the class member roleIdFk.
     *
     * @param integer $roleIdFk Holds the value for the class member roleIdFk
     */
    public function setRoleIdFk($roleIdFk = null) {
        $this->roleIdFk = $roleIdFk;
    }

    /**
     * Returns the value of the class member userIdFk.
     *
     * @return integer Holds the value of the class member userIdFk
     */
    public function getUserIdFk() {
        return $this->userIdFk;
    }

    /**
     * Sets the value for the class member userIdFk.
     *
     * @param integer $userIdFk Holds the value for the class member userIdFk
     */
    public function setUserIdFk($userIdFk = null) {
        $this->userIdFk = $userIdFk;
    }

    /**
     * Returns the value of the class member name.
     *
     * @return string Holds the value of the class member name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the value for the class member name.
     *
     * @param string $name Holds the value for the class member name
     */
    public function setName($name) {
        $this->name = $name;
    }

}