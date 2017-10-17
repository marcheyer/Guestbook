<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\TimestampableTrait;
use Serializable;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="Email is already in use")
 * @UniqueEntity(fields="username", message="Username is already in use")
 */
class User implements AdvancedUserInterface, Serializable
{
    use TimestampableTrait;
    /**
     * ID
     *
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Username
     *
     * @var string
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;

    /**
     * Password
     *
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * E-Mail
     *
     * @var string
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * Roles
     *
     * @var array
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * is Active
     *
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $active;

    /**
     * User constructor.
     *
     * @param $roles
     */
    public function __construct($roles)
    {
        $this->active = true;
        $this->roles = $roles;
        $this->setCreatedAt(new \DateTime());
        $this->setUpdatedAt($this->getCreatedAt());
        // may not be needed, see section on salt below
        // $this->salt = md5(uniqid('', true));
    }

    /**
     * get Username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * get ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * get E-Mail
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * get isActive
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }


    /**
     * set Username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * set Password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * set E-Mail
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * set isActive
     *
     * @param bool $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * get Salt
     *
     * @return null
     */
    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    /**
     * get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * get Roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * erase Credentials
     */
    public function eraseCredentials()
    {
    }

    /** Serialize User
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * Unserialize User
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->active;
    }
}

