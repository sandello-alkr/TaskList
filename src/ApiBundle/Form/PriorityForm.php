<?php
namespace ApiBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PriorityForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('level');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'csrf_protection' => false,
                'data_class' => 'ApiBundle\Entity\Priority'
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }
}