<?php

declare(strict_types=1);

namespace DMKClub\Bundle\BasicsBundle\Form\Type;

use DMKClub\Bundle\BasicsBundle\Entity\TwigTemplate;
use DMKClub\Bundle\BasicsBundle\PDF\Manager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TwigTemplateType extends AbstractType
{
    public function __construct(
        private readonly Manager $pdfManager
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
    }

    private function buildPlainFields(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'label' => 'dmkclub.basics.twigtemplate.name.label',
        ])
            ->add('template', TextareaType::class, [
                'required' => false,
                'label' => 'dmkclub.basics.twigtemplate.template.label',
                'attr' => [
                    'class' => 'template-editor',
                    'style' => 'width:100%;min-height:300px',
                    // 'data-wysiwyg-enabled' => true,
                ],
                // 'wysiwyg_options' => [
                // 'height' => '250px'
                // ]
            ])
            ->add('generator', ChoiceType::class, [
                'required' => false,
                'label' => 'dmkclub.basics.twigtemplate.generator.label',
                'choices' => $this->pdfManager->getVisibleGeneratorChoices(),
                'placeholder' => 'dmkclub.form.choose',
            ])
            ->add('orientation', ChoiceType::class, [
                'label' => 'dmkclub.basics.twigtemplate.orientation.label',
                'choices' => [
                    'P' => 'Portrait',
                    'L' => 'Landscape',
                ],
            ])
            ->add('pageFormat', TextareaType::class, [
                'required' => true,
                'label' => 'dmkclub.basics.twigtemplate.page_format.label',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TwigTemplate::class,
            'cascade_validation' => true,
        ]);
    }
}
