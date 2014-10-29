<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *  This class aims to configure the form creating users
 */
class UserType extends AbstractType
{
	private $firstname;
	private $lastname;
	private $email;
	private $roles;
	private $admin;
	
	public function __construct($user = NULL, $admin = NULL) {
		$this->firstname = $user ? $user->getFirstname() : '';
		$this->lastname = $user ? $user->getLastname() : '';
		$this->email = $user ? $user->getEmail() : '';
		$roles = $user ? $user->getRoles() : '';
		$this->roles = $roles ? $roles[0] : '';
		$this->admin = $admin;
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	if ($this->admin) {
	        $builder
	            ->add('firstname', 'text', array('data' => $this->firstname))
	            ->add('lastname', 'text', array('data' => $this->lastname))
	            ->add('email', 'email', array('data' => $this->email))
	            ->add('password', 'password', array('required' => TRUE))
	            ->add('confirm_password', 'password', array())
				->add('pictureAccount', 'file', array('required' => TRUE))
				->add('question', 'choice', 
					array(
						'choices' => array(
							'q1' => 'In which town am I born?',
							'q2' => 'What is the name of my first pet?',
							'q3' => 'What is my favorite movie?',
							'q4' => 'What is my favorite song?',
						)))
	            ->add('answer', 'text', array('required' => TRUE))
	        ;
		} else {
	        $builder
	            ->add('firstname', 'text', array('data' => $this->firstname))
	            ->add('lastname', 'text', array('data' => $this->lastname))
	            ->add('email', 'email', array('data' => $this->email))
	            ->add('password', 'password', array('required' => TRUE))
	            ->add('confirm_password', 'password', array())
				->add('usertype', 'choice', 
					array(
						'choices' => array(
							'user' => 'User',
							'admin' => 'Admin',
						),
						'preferred_choices' => array($this->roles ? $this->roles : 'ROLE_ADMIN'),
					))
				->add('pictureAccount', 'file', array('required' => TRUE))
					->add('question', 'choice', 
					array(
						'choices' => array(
							'q1' => 'In which town am I born?',
							'q2' => 'What is the name of my first pet?',
							'q3' => 'What is my favorite movie?',
							'q4' => 'What is my favorite song?',
						)))
	            ->add('answer', 'text', array('required' => TRUE))						
	        ;
		}
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AcsilServer\AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'acsilserver_appbundle_usertype';
    }
}
