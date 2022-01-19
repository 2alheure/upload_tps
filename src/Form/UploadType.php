<?php

namespace App\Form;

use App\Entity\Upload;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UploadType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('render_link', UrlType::class, [
                'label' => 'Rendu (lien)',
                'required' => false
            ])
            ->add('render_file', FileType::class, [
                'label' => 'Rendu (fichier)',
                'required' => false,
                'mapped' => false
            ]);

        if ($options['is_update'])
            $builder->add('drop_file', CheckboxType::class, [
                'label' => 'Supprimer le fichier ?',
                'mapped' => false,
                'required' => false
            ]);

        $builder->add('comment', TextareaType::class, [
            'label' => 'Commentaire',
            'required' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        $resolver->setDefaults([
            'data_class' => Upload::class,
            'is_update' => false
        ]);

        $resolver->setAllowedTypes('is_update', 'bool');
    }
}
