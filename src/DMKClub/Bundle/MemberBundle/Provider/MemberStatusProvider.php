<?php
namespace DMKClub\Bundle\MemberBundle\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;
use DMKClub\Bundle\MemberBundle\Model\MemberStatus;

class MemberStatusProvider
{

    /**
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     *
     * @var array
     */
    protected $choices = [
        MemberStatus::PROPOSAL => 'dmkclub.member.status.proposal',
        MemberStatus::ACTIVE => 'dmkclub.member.status.active',
        MemberStatus::SUSPENDED => 'dmkclub.member.status.suspended',
        MemberStatus::TERMINATED => 'dmkclub.member.status.terminated'
    ];

    /**
     *
     * @var array
     */
    protected $translatedChoices;

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
     * @return array
     */
    public function getChoices()
    {
        if (null === $this->translatedChoices) {
            $this->translatedChoices = [];
            foreach ($this->choices as $name => $label) {
                $this->translatedChoices[$this->translator->trans($label)] = $name;
            }
        }

        return $this->translatedChoices;
    }

    /**
     *
     * @param string $name
     * @return string
     * @throws \LogicException
     */
    public function getLabelByName($name)
    {
        $choices = array_flip($this->getChoices());
        if (! isset($choices[$name])) {
            throw new \LogicException(sprintf('Unknown member status with name "%s"', $name));
        }

        return $choices[$name];
    }
}
