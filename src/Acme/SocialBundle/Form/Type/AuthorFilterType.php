<?php

namespace Acme\SocialBundle\Form\Type;

use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name', 'filter_text',array('condition_pattern'=> FilterOperands::STRING_BOTH))
            ->add('screen_name', 'filter_text',array('condition_pattern'=> FilterOperands::STRING_BOTH))
            ->add('location', 'filter_text',array('condition_pattern'=> FilterOperands::STRING_BOTH))
            ->add('description', 'filter_text')
            ->add('created_at', 'filter_date_range')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'SocialBundle\Entity\Author',
            'csrf_protection'   => false,
            'validation_groups' => array('filter'),
            'method'            => 'GET',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'author_filter';
    }
}
