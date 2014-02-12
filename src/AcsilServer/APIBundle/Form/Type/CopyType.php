<?php

// src/Acsilserver/APIBundle/Form/Type/CopyType.php
namespace AcsilServer\APIBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fromId', 'integer');
        $builder->add('toPath', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AcsilServer\APIBundle\Entity\Copy',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'copy';
    }
}

?>
