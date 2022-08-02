<?php

namespace DMKClub\Bundle\MemberBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\FormBundle\Form\Type\OroDateType;
use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use DMKClub\Bundle\MemberBundle\Model\AgePrice;

/**
 * Preisangabe für eine Altersgruppe.
 * Angegeben werden die folgenden Werte:
 * - Alter von
 * - Alter bis
 * - Label
 * - monatlicher Preis
 */
class AgePriceType extends AbstractType {

// 	/** @var TranslatorInterface */
// 	protected $translator;

// 	/**
// 	 * @param TranslatorInterface $translator
// 	 */
// 	public function __construct(TranslatorInterface $translator)
// 	{
// 		$this->translator = $translator;
// 	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array                $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
	    $labelPrefix = 'dmkclub.member.memberbilling.fee_age.';
	    $builder
    	    ->add(DefaultProcessor::OPTION_FEE_AGE_VALUE, MoneyType::class, [
    	        'required' => false,
    	        'divisor' => 100,
    	        'attr' => [
    	            'placeholder' => $labelPrefix.'value',
    	        ],
    	    ])
    	    ->add(DefaultProcessor::OPTION_FEE_AGE_FROM, IntegerType::class, [
    	        'required' => true,
    	        'attr' => [
    	            'placeholder' => $labelPrefix.'from',
    	        ],
    	    ])
    	    ->add(DefaultProcessor::OPTION_FEE_AGE_TO, IntegerType::class, [
    	        'required' => true,
    	        'attr' => [
    	            'placeholder' => $labelPrefix.'to',
    	        ],
    	    ]);
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(
	        [
	            'cascade_validation' => true,
	            'data_class' => AgePrice::class,
	        ]
	    );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
	    return $this->getBlockPrefix();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
	    return 'dmkclub_member_ageprice';
	}
}
