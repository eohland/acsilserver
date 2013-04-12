<?php
// src/AcsilServer/AppBundle/Entity/User.php
namespace AcsilServer\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * AcsilServer\AppBundle\Entity\User
 *
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="AcsilServer\AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
	
    /**
     * @ORM\Column(type="string", length=50)
     */
	private $firstname;
	
	/**
     * @ORM\Column(type="string", length=50)
     */
	private $lastname;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $salt;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $password;
	
	private $confirm_password;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $usertype;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $roles;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

	  /**
     * @ORM\Column(type="string", length=255)
     */
	private $pictureAccount;
	
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = true;
        $this->salt = md5(uniqid(null, true));
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    
        return $this;
    }

	   /**
     * Set pictureAccount
     *
     * @param string $pictureAccount
     * @return User
     */
    public function setPictureAccount($pictureAccount)
    {
        $this->pictureAccount = $pictureAccount;
    
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Get pictureAccount
     *
     * @return string 
     */
    public function getPictureAccount()
    {
        return $this->pictureAccount;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
    
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setConfirmPassword($confirmPassword)
    {
        $this->confirm_password = $confirmPassword;
    
        return $this;
    }

    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * Set usertype
     *
     * @param string $roles
     * @return User
     */
    public function setUsertype($usertype)
    {
        $this->usertype = $usertype;
    
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsertype()
    {
        return $this->usertype;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return json_decode($this->roles);
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return User
     */
    public function setCreationDate(\DateTime $creationDate = NULL)
    {
        $this->creationDate = $creationDate;
    
        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
	
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }

	public function isEqualTo(UserInterface $user)
	{
	    return $this->username === $user->getUsername();
	}
	
	public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }
}