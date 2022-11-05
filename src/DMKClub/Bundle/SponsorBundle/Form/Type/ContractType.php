<?php
namespace DMKClub\Bundle\SponsorBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\ContactBundle\Form\Type\ContactSelectType;
use Oro\Bundle\AccountBundle\Form\Type\AccountSelectType;
use Oro\Bundle\ChannelBundle\Form\Type\ChannelSelectType;
use Oro\Bundle\AddressBundle\Form\Type\AddressType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use Oro\Bundle\AttachmentBundle\Form\Type\MultiFileType;
use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Oro\Bundle\EntityExtendBundle\Form\Type\EnumSelectType;
use DMKClub\Bundle\SponsorBundle\Model\ContractShipping;

class ContractType extends AbstractType
{
    const LABEL_PREFIX = 'dmkclub.sponsor.contract.';

    /** @var TranslatorInterface */
    protected $translator;

    /**
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->buildPlainFields($builder, $options);
        $this->buildRelationFields($builder, $options);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected function buildPlainFields(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
                'required' => true,
                'label' => self::LABEL_PREFIX . 'name.label'
            ])
        ;
        $builder->add('beginDate', OroDateType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX . 'begin_date.label'
        ])
        ->add('endDate', OroDateType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX . 'end_date.label'
        ])
        ->add('shippingWay', EnumSelectType::class, [
            'required' => true,
            'label' => self::LABEL_PREFIX . 'shipping_way.label',
            'enum_code' => ContractShipping::INTERNAL_ENUM_CODE
        ])
        ->add('attachment', FileType::class, [
            'label' => self::LABEL_PREFIX . 'attachment.label',
            'required' => false

        ]);


        $builder->add('totalAmount', MoneyType::class, [
            'required' => false,
//            'divisor' => 100,
            'label' => self::LABEL_PREFIX . 'total_amount.label'
        ]);
    }

    /**
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function buildRelationFields(FormBuilderInterface $builder, array $options)
    {
        // contract categories
        // sponsor categories
        $builder->add('category', ContractCategorySelectType::class, [
            'label' => self::LABEL_PREFIX . 'category.label',
            'required' => false
        ]);
    }

    /**
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \DMKClub\Bundle\SponsorBundle\Entity\Contract::class,
            'cascade_validation' => true
        ]);
    }
}
