<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Exercice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ExerciceType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'name',
            ])
            ->add('subject_link', UrlType::class, [
                'label' => 'Sujet (lien)'
            ])
            ->add('subject_file', FileType::class, [
                'label' => 'Sujet (fichier)',
                'mapped' => false
            ]);

        if ($options['is_update'])
            $builder->add('drop_file', CheckboxType::class, [
                'label' => 'Supprimer le fichier ?',
                'mapped' => false
            ]);

        $builder->add('comment', TextareaType::class, [
            'label' => 'Commentaire'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Exercice::class,
            'is_update' => false
        ]);

        $resolver->setAllowedTypes('is_update', 'bool');
    }
}
