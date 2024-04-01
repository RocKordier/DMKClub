<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorySelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'dmkclub_sponsorcategories', // Der Alias wird vom search_handler verwendet
                'create_form_route' => 'dmkclub_sponsorcategory_create',
                'configs' => [
                    'placeholder' => 'dmkclub.sponsor.form.choose_category',
                ],
            ]
        );
    }

    public function getParent(): string
    {
        return OroEntitySelectOrCreateInlineType::class;
    }
}
