<?php

namespace Acme\SocialBundle\Form\Type;

use Lexik\Bundle\FormFilterBundle\Filter\FilterOperands;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;

class TweetFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('text', 'filter_text',array('condition_pattern'=> FilterOperands::STRING_BOTH))
            ->add('created_at', 'filter_date_range')
            ->add('status', 'filter_text')
            ->add('tags', 'filter_entity', array('class' => 'SocialBundle\Entity\Tag',
                'apply_filter' => function(QueryInterface $filterQuery, $field, $values)
                {
                    if ($values['value'])
                    {
                        $qb = $filterQuery->getQueryBuilder();
                        $expr = $qb->expr();

                        $qb->leftJoin( $values['alias'].'.tags', 't0');

                        $qb->andWhere($expr->in('t0.name',"'".$values['value']->getName()."'" )); // here $values['value'] will be a collection of objects so maybe you will have to transform it into an array of ids to make the `in` expression work correctly.
                    }
                    return $filterQuery;
                }
            ))
            ->add('author', 'filter_entity', array('class' => 'SocialBundle\Entity\Author'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'SocialBundle\Entity\Tweet',
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
        return 'tweet_filter';
    }
}
