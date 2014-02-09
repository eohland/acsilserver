<?php
// src/AcsilServer/APIBundle/Form/Type/FileType.php
// namespace AcsilServer\APIBundle\Form\Type;
namespace AcsilServer\APIBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Request;


class FileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
		$builder->add('id');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'AcsilServer\AppBundle\Entity\Document',
            'csrf_protection'   => false,
        ));
    }


    public function getName()
    {
        return 'name';
    }
	public function getId()
    {
        return 'id';
    }
}
