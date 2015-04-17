<?php

namespace Acme\SocialBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AuthorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('name')
            ->add('screen_name')
            ->add('code')
            ->add('profile_image_url')
            ->add('location')
            ->add('description')
            ->add('created_at')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SocialBundle\Entity\Author',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'author';
    }
}
