<?php

namespace App\Form;

use App\Entity\Promo;
use App\Entity\Render;
use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RenderType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('dateBegin', DateTimeType::class, [
                'label' => 'Début',
                'widget' => 'single_text',
                'with_seconds' => true,
            ])
            ->add('dateEnd', DateTimeType::class, [
                'label' => 'Fin',
                'widget' => 'single_text',
                'with_seconds' => true,
            ])
            ->add('promo', EntityType::class, [
                'class' => Promo::class,
                'choice_label' => 'name'
            ])
            ->add('exercice', EntityType::class, [
                'class' => Exercice::class,
                'choice_label' => function ($exercice) {
                    return $exercice->getName() . ' - ' . $exercice->getModule()->getName();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Render::class,
        ]);
    }
}
