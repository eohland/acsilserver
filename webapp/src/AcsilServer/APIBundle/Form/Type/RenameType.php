<?php

// src/Acsilserver/APIBundle/Form/Type/RenameType.php
namespace AcsilServer\APIBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RenameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fromId', 'integer');
        $builder->add('toName', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AcsilServer\APIBundle\Entity\Rename',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'rename';
    }
}

?>
