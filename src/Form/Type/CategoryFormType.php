<?php

namespace App\Form\Type;

use App\Form\Model\CategoryDto;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', TextType::class)
            ->add('name', TextType::class);
        $builder->get('id')->addModelTransformer(new CallbackTransformer(
        /**
         * @param null|UuidInterface $id
         * @return string
         */
            function(?UuidInterface $id) {
                if($id === null) {
                    return '';
                }
                return $id->toString();
            },
            function($id) {
                return $id === null ? null : Uuid::fromString($id);
            }
        ));
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryDto::class,
            'csrf_protection' => false,
        ]);
    }
    
    public function getBlockPrefix(): string
    {
        return '';
    }
    
    public function getName(): string
    {
        return '';
    }
}