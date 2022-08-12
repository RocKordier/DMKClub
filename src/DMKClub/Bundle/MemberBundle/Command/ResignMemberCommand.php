<?php
namespace DMKClub\Bundle\MemberBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\CronBundle\Command\CronCommandInterface;

use DMKClub\Bundle\MemberBundle\Entity\Manager\MemberManager;

/**
 * Send membership fees to member by email.
 */
class ResignMemberCommand extends Command implements CronCommandInterface
{

    const NAME = 'oro:cron:dmkclub:member:resign';


    protected $output;

    /**
     *
     * @var MemberManager
     */
    private $mbrMgr;

    public function __construct(MemberManager $mbrMgr)
    {
        parent::__construct();
        $this->mbrMgr = $mbrMgr;
    }

    /**
     *
     * @return ConfigManager
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setDescription('Command to update member status if resignation date is reached.');
    }


    public function isActive()
    {
        return true;
    }

    public function getDefaultDefinition()
    {
        return '10 0 * * *';
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<info>Resigned %d members.</info>', $this->mbrMgr->checkResignedMembers()));
    }
}
