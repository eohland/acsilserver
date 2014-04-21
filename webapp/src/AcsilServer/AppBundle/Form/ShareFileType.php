<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This class aims to configure the form about sharing a file
 */
class ShareFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userMail', 'email', array('required' => TRUE))
            ->add('rights', 'choice', 
            	array(
					'choices' => array(
						'VIEW' => 'View',
						'EDIT' => 'Edit'
					)
				))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AcsilServer\AppBundle\Entity\ShareFile'
        ));
    }

    public function getName()
    {
        return 'acsilserver_appbundle_sharefiletype';
    }
}
