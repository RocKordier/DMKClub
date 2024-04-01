<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use Doctrine\ORM\EntityRepository;
use Oro\Bundle\FormBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwigTemplateSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'dmkclub_twigtemplates',
                'class' => TwigTemplate::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'configs' => [
                    'allowClear' => true,
                    'placeholder' => 'dmkclub.form.choose',
                ],
                'empty_value' => '',
                'empty_data' => null,
            ]
        );
    }

    public function getParent(): string
    {
        return Select2EntityType::class;
    }
}
