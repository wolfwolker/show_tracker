<?php

namespace WW\WWShowTrackerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use WW\WWShowTrackerBundle\Entity\Show;

class TrackCommand extends ContainerAwareCommand
{
	const MIN_EP_TO_CHECK_SEASON = 5;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('tracker:track');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
		$container = $this->getContainer();
		$manager = $container->get("doctrine.orm.entity_manager");
		$shows = $manager->getRepository("WWShowTrackerBundle:Show")->findByActive(true);
		$found = [];

		/** @var Show $show */
		foreach($shows as $show)
		{
			if($this->doCheck($show, $show->getNext(), $found))
				$output->writeln("New episode for $show");
			else if($show->getEpisode() >= self::MIN_EP_TO_CHECK_SEASON && $show->getEpisode() >= $show->getCheckNextSeason() && $this->doCheck($show, $show->getNext(false), $found))
				$output->writeln("New season for $show");
			else
				$output->writeln("No new content for $show :(");
		}

		if($found)
		{
			$message = \Swift_Message::newInstance()
				->setSubject('Nuevos episodios!')
				->setFrom('noelcoolmobile@gmail.com')
			;
			$mailer = $container->get("mailer");

			foreach($found as $mail => $content)
			{
				$message
					->setTo($mail)
					->setBody(implode("\n", $content))
				;
				$mailer->send($message);
			}

			$spool = $mailer->getTransport()->getSpool();
			$spool->flushQueue($container->get('swiftmailer.transport.real'));


			$manager->flush();
		}
    }

	private function doCheck(Show $show, array $data, array &$found = array())
	{
		$url = str_replace(array_keys($data), array_values($data), $show->getUrl());
		if($this->check($url))
		{
			foreach($show->getUsers() as $user)
				$found[(string)$user][] = $url;
			$show->update($data);
			return true;
		}
		return false;
	}

	private function check($url)
	{
		$result = @file_get_contents($url);
		return (bool)strlen($result);
	}
}
