<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('question', TextareaType::class, [
        'label' => false,
        'attr' => [
          'placeholder' => 'Type your message...',
          'class' => 'w-full rounded border border-gray-400 py-2 px-4 mr-2'
        ]
      ])
      ->add('send', SubmitType::class, [
        'label' => 'Send',
        'attr' => [
          'class' => 'bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
        ]
      ])
    ;
  }
}