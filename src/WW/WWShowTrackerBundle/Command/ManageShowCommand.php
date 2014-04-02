<?php

namespace WW\WWShowTrackerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WW\WWShowTrackerBundle\Entity\Show;

class ManageShowCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
			->setName('tracker:manage_show')
			->addArgument('action', InputArgument::REQUIRED)
		;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$action = $input->getArgument("action");
		$manager = $this->getContainer()->get("doctrine.orm.entity_manager");
		/** @var DialogHelper $dialogs */
		$dialogs = $this->getHelper("dialog");
		$name = $dialogs->ask($output, "Get me the show name:\n");

		if($action == 'add')
		{
			$show = new Show();
			$show
				->setName($name)
				->setUrl($dialogs->ask($output, 'Url: '))
				->setSeason($dialogs->ask($output, 'Current season: '))
				->setEpisode($data = $dialogs->ask($output, 'Current episode: '))
			;

			$manager->persist($show);
		}
		else if($show = $manager->getRepository("WWShowTrackerBundle:Show")->findOneByName($name))
		{
			$show->setActive($action!='disable');
		}
		else
			$output->writeln('Nothing to do here!');

		$manager->flush();
    }
}
