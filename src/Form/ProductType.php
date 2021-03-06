<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('quantity')
            ->add('description')
            ->add('image',
                    FileType::class,
                    [
                        'data_class' => null,
                        'required' => is_null($builder->getData()->getImage())
                    ]
            )
            ->add('catName',
                    EntityType::class,
                    [
                        'class' => Category::class,
                        'choice_label' => 'catName',
                        'multiple' => false,
                        'expanded' => false
                    ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
