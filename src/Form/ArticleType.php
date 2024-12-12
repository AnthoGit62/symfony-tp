<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['attr' => ['class' => 'form-control']], ['label_attr' => ['class' => 'form-label mt-2']])
            ->add('texte', TextareaType::class, ['attr' => ['class' => 'form-control']], ['label_attr' => ['class' => 'form-label mt-2']])
            ->add('publie', CheckboxType::class , ['attr' => ['class' => 'form-check-input']], ['label_attr' => ['class' => 'form-check-label mt-1']])
            ->add('date', DateTimeType::class, ['widget' => 'single_text', 'attr' => ['class' => 'form_control']], ['label_attr' => ['class' => 'form-label mt-2']])
            ->add('image', FileType::class, ['label' => 'Image à mettre','mapped' => false,'required' => false,'constraints' => [
                new File(['maxSize' => '1024k','mimeTypes' => ['image/png','image/jpg','image/jpg',],'mimeTypesMessage' => 'Format de fichier invalide(png/jpg/jpeg)',])],])
                ->add('categorie', EntityType::class, [
                    'class' => Categorie::class,
                    'choice_label' => 'nom', 
                    'attr' => ['class' => 'form-control'],
                    'label_attr' => ['class' => 'form-label mt-2'],
                ])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-primary mb-3']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
