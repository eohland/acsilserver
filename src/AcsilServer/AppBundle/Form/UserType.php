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
	
	public function __construct($user) {
		$this->firstname = $user->getFirstname() ? $user->getFirstname() : '';
		$this->lastname = $user->getLastname() ? $user->getLastname() : '';
		$this->email = $user->getEmail() ? $user->getEmail() : '';
		$roles = $user->getRoles();
		$this->roles = $roles[0] ? $roles[0] : '';
	}
	
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array('data' => $this->firstname))
            ->add('lastname', 'text', array('data' => $this->lastname))
            ->add('email', 'email', array('data' => $this->email))
            ->add('password', 'password', array())
            ->add('confirm_password', 'password', array())
			->add('roles', 'choice', 
				array(
					'choices' => array(
						'ROLE_SUPER_ADMIN' => 'Admin',
						'ROLE_ADMIN' => 'User',
					),
					'preferred_choices' => array($this->roles),
				))
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
