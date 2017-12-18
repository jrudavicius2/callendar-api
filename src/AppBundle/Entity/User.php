<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    /**
     * @var int
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }


//    /**
//     * @var int
//     */
//    private $id;
//
//    /**
//     * @var string
//     */
//    private $fullName;
//
//    /**
//     * @var string
//     */
//    private $email;
//
//    /**
//     * @var string
//     */
//    private $password;
//
//    /**
//     * @return int
//     */
//    public function getId()
//    {
//        return $this->id;
//    }
//
//    /**
//     * @param int $id
//     */
//    public function setId($id)
//    {
//        $this->id = $id;
//    }
//
//    /**
//     * @return string
//     */
//    public function getFullName()
//    {
//        return $this->fullName;
//    }
//
//    /**
//     * @param string $fullName
//     */
//    public function setFullName($fullName)
//    {
//        $this->fullName = $fullName;
//    }
//
//    /**
//     * @return string
//     */
//    public function getEmail()
//    {
//        return $this->email;
//    }
//
//    /**
//     * @param string $email
//     */
//    public function setEmail($email)
//    {
//        $this->email = $email;
//    }
//
//    /**
//     * @return string
//     */
//    public function getPassword()
//    {
//        return $this->password;
//    }
//
//    /**
//     * @param string $password
//     */
//    public function setPassword($password)
//    {
//        $this->password = $password;
//    }
}
