<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $regionBundle = Intl::getRegionBundle();
        $cnames = $regionBundle->getCountryNames();
        $x = ['country', ChoiceType::class, [
        'choices' => array_flip(Intl::getRegionBundle()->getCountryNames())
        ]];
        $builder->add('videoId')
            ->add('title')
            ->add('config')
            ->add('description')
            ->add('isPlaylist')
            ->add('image')
            ->add('created')
            ->add('updated')
            ->add('duration')
            ->add('available')
            ->add('author')
            ->add('uploadDate')
            ->add('items');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Video'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_video';
    }


}
