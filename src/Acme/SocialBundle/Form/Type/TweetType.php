<?php

namespace Acme\SocialBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TweetType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('text')
            ->add('code')
            ->add('created_at')
            ->add('status')
            ->add('tags')
            ->add('author')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SocialBundle\Entity\Tweet',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tweet';
    }
}
