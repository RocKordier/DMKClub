<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class DefaultProcessorSettingsType extends AbstractProcessorSettingsType
{
//    const NAME = 'dmkclub_member_default_processor_settings';

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $labelPrefix = 'dmkclub.member.memberbilling.';
        $builder
            ->add(DefaultProcessor::OPTION_FEE, MoneyType::class, [
            		'required' => true,
            		'divisor' => 100,
            		'label' => $labelPrefix.'fee.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_ADMISSION, MoneyType::class, [
            		'required' => true,
            		'divisor' => 100,
            		'label' => $labelPrefix.DefaultProcessor::OPTION_FEE_ADMISSION.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_DISCOUNT, MoneyType::class, [
            		'required' => false,
            		'divisor' => 100,
            		'label' => $labelPrefix.DefaultProcessor::OPTION_FEE_DISCOUNT.'.label'
            ])
            ->add(DefaultProcessor::OPTION_FEE_AGE_RAISE_ON_BIRTHDAY, CheckboxType::class, [
                'required' => false,
                'tooltip' => $labelPrefix.DefaultProcessor::OPTION_FEE_AGE_RAISE_ON_BIRTHDAY.'.tooltip',
                'label' => $labelPrefix.DefaultProcessor::OPTION_FEE_AGE_RAISE_ON_BIRTHDAY.'.label'
            ])
        ;
        $builder
            ->add(
                DefaultProcessor::OPTION_FEE_AGES,
                CollectionType::class,
                [
                    'label'          => $labelPrefix.DefaultProcessor::OPTION_FEE_AGES.'.label',
                    'tooltip'        => $labelPrefix.DefaultProcessor::OPTION_FEE_AGES.'.tooltip',
                    'add_label'      => $labelPrefix.DefaultProcessor::OPTION_FEE_AGES.'.add',
                    'entry_type'     => AgePriceType::class,
                    'allow_add'      => true,
                    'allow_delete'   => true,
                    'by_reference'   => true,
                    'prototype'      => true,
                    'prototype_name' => 'tag__name__'
                ]
            )
        ;
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return DefaultProcessorSettingsType::class;
    }
}
