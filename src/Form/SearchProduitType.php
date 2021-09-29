<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Search\SearchProduit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filtrerParNom',TextType::class, [
                'label' => 'Filtrer par nom',
                'required' => false,
            ])
            ->add('filtrerParCategorie', EntityType::class, [
                'label' => 'Filtrer par catégorie',
                'required' => false,
                'placeholder' => '-- Choisir par catégorie --',
                'class' => Categorie::class,
                'choice_label' => function (Categorie $category) {
                    return strtoupper($category->getNom());
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchProduit::class,
            'method' => 'get',
            'csrf_protection' => false
        ]);
    }
}
