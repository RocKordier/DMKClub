<?php

declare(strict_types=1);

namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use DMKClub\Bundle\SponsorBundle\Entity\Contract;
use DMKClub\Bundle\SponsorBundle\Model\ContractShipping;
use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContractType extends AbstractType
{
    public const string LABEL_PREFIX = 'dmkclub.sponsor.contract.';

    public function __construct(
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }

    private function buildPlainFields(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'name.label',
        ])
        ;
        $builder->add('beginDate', OroDateType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'begin_date.label',
        ])
        ->add('endDate', OroDateType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'end_date.label',
        ])
        ->add('shippingWay', EnumSelectType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX.'shipping_way.label',
            'enum_code' => ContractShipping::INTERNAL_ENUM_CODE,
        ])
        ->add('attachment', FileType::class, [
            'label' => self::LABEL_PREFIX.'attachment.label',
            'required' => false,
        ]);

        $builder->add('totalAmount', MoneyType::class, [
            'required' => false,
            //            'divisor' => 100,
            'label' => self::LABEL_PREFIX.'total_amount.label',
        ]);
    }

    private function buildRelationFields(FormBuilderInterface $builder, array $options): void
    {
        // contract categories
        // sponsor categories
        $builder->add('category', ContractCategorySelectType::class, [
            'label' => self::LABEL_PREFIX.'category.label',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contract::class,
            'cascade_validation' => true,
        ]);
    }
}
