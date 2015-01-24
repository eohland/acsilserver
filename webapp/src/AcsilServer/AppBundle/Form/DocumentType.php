<?php

namespace AcsilServer\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * This class aims to configure the form for a document
 */
class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',  'text', array('required' => TRUE))
            ->add('file', 'file', array('required' => TRUE))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
				     'data_class' => 'AcsilServer\AppBundle\Entity\Document',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'acsilserver_appbundle_documenttype';
    }
}
