<?php

// src/Acsilserver/APIBundle/Form/Type/DeleteType.php
namespace AcsilServer\APIBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('deleteId', 'integer');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AcsilServer\APIBundle\Entity\Delete',
            'csrf_protection'   => false,
        ));
    }

    public function getName()
    {
        return 'delete';
    }
}

?>
