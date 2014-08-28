<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/** 
 * This class aims to configure the form for changing password
 */
class ChangePwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pwd',  'password', array('required' => TRUE))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AcsilServer\AppBundle\Entity\ChangePwd'
        ));
    }

    public function getName()
    {
        return 'acsilserver_appbundle_changepwdtype';
    }
}
