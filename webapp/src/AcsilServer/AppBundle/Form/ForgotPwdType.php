<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/** 
 * This class aims to configure the form for forgot password
 */
class ForgotPwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('email', 'email', array('required' => TRUE))
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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AcsilServer\AppBundle\Entity\ForgotPwd'
        ));
    }

    public function getName()
    {
        return 'acsilserver_appbundle_forgotpwdtype';
    }
}