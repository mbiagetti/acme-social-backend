<?php

namespace Acme\SocialBundle\Form\Type;

use SocialBundle\Entity\Tweet;
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
            ->add('status','choice', array(
                'choices' => array(
                    Tweet::PENDING => 'tweet.status_'.Tweet::PENDING,
                    Tweet::ACCEPTED => 'Approved',
                    Tweet::DECLINED => 'Declined',
                )))
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
