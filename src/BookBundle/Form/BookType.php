<?php

namespace BookBundle\Form;


use BookBundle\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('image', FileType::class)
            ->add('title', TextType::class,[
                'attr' => [
                    'maxlength' => '255',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])
            ->add('author', TextType::class, [
                'attr' => [
                    'maxlength' => '255',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])
            ->add('summary', TextareaType::class, [
                'attr' => [
                    'maxlength' => '1000',
                    'rows' => '6',
                    'cols' => '100',
                    'class' => 'reviewBox',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])
            ->add('category', TextType::class, [
                'attr' => [
                    'maxlength' => '255',
                    'pattern' => '[a-zA-Z0-9]*'
                ]
            ])

            ->add('save', SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Book::class,
        ));
    }
}