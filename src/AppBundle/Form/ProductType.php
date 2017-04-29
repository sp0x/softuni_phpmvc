<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\Category;
use Doctrine\DBAL\Types\IntegerType;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$categoryRepo = $options['category_repository'];
        $builder->add('name', TextType::class)
            ->add('cost', CurrencyType::class)
            ->add('order', IntegerType::class)
            ->add('isAvailable')
            ->add('createdOn')
            ->add('description', TextareaType::class)
            ->add('image_form', FileType::class,  [
                'data_class' => null,
                'required' => false
            ] )
            ->add('category', EntityType::class ,[
                'class' => Category::class,
                'choice_label' => 'name'
//                'queryBuilder' => function(EntityRepository $er){
//                    return $er->createQueryBuilder('q')
//                        ->orderBy('q.name', 'ASC');
//                }
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_product';
    }


}
