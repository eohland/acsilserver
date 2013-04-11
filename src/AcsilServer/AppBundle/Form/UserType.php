<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
	private $firstname;
	private $lastname;
	private $email;
	private $roles;
	
	public function __construct($user = NULL) {
		$this->firstname = $user ? $user->getFirstname() : '';
		$this->lastname = $user ? $user->getLastname() : '';
		$this->email = $user ? $user->getEmail() : '';
		$roles = $user ? $user->getRoles() : '';
		$this->roles = $roles ? $roles[0] : '';
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array('data' => $this->firstname))
            ->add('lastname', 'text', array('data' => $this->lastname))
            ->add('email', 'email', array('data' => $this->email))
            ->add('password', 'password', array('required' => FALSE))
            ->add('confirm_password', 'password', array())
			->add('roles', 'choice', 
				array(
					'choices' => array(
						'ROLE_ADMIN' => 'User',
						'ROLE_SUPER_ADMIN' => 'Admin',
					),
					'preferred_choices' => array($this->roles ? $this->roles : 'ROLE_ADMIN'),
				))
			->add('pictureAccount', 'file', array('required' => FALSE))
        ;
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
