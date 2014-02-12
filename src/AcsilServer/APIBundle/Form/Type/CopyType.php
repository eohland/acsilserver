<?php

// src/Acsilserver/APIBundle/Form/Type/CopyType.php
namespace Acsilserver\APIBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CopyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('from_id', 'integer');
        $builder->add('to_path', 'text');
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
