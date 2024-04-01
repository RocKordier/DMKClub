<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use DMKClub\Bundle\SponsorBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildPlainFields($builder, $options);
    }

    private function buildPlainFields(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'required' => true,
            'label' => 'dmkclub.member.name.label',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'cascade_validation' => true,
        ]);
    }
}
