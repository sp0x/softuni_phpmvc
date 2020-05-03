<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('productId', IntegerType::class, [
            'required' => false
        ])
            ->add('category',  EntityType::class ,[
                'class' => Category::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('isGeneral' , CheckboxType::class, [
                'required' => false
            ])
            ->add('start', DateTimeType::class)
            ->add('ends', DateTimeType::class)
            ->add('criteria', ChoiceType::class,[
                'required' => false,
                'choices' => [
                    'User has more than 100 credit' => "USER_CREDIT_100",
                    'User registered for 1 day at least' => "USER_REGISTERED_1D",
                    'User is admin'=> "USER_IS_ADMIN"
                ]

            ])
            ->add('discount', PercentType::class,[
                'type' => 'integer'
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Promotion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_promotion';
    }


}
