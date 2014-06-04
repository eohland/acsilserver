<?php
// src/AcsilServer/APIBundle/Command/CreateClient.php
namespace AcsilServer\APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AcsilServer\APIBundle\Client;

class ClientCreateCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('acsilserver:api:client:create')
      ->setDescription('Creates a new client')
      ->addArgument('name', InputArgument::REQUIRED, 'Sets the client name', null)
      ->addOption(
        'redirect-uri',
        null,
        InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
        'Sets redirect uri for client. ',
        null
      )
      ->addOption(
        'grant-type',
        null,
        InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
        'Sets allowed grant type for client. ',
        null
      )
      ->setHelp(
        'The <info>%command.name%</info> command creates a new client.'.PHP_EOL.
          '  <info>php %command.full_name% [--redirect-uri=...] [--grant-type=...] name</info>'
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
    $client = $clientManager->createClient();
    $client->setName($input->getArgument('name'));
    $client->setRedirectUris($input->getOption('redirect-uri'));
    $client->setAllowedGrantTypes($input->getOption('grant-type'));
    $clientManager->updateClient($client);
    $output->writeln(
      sprintf(
        'Added a new client <info>%s</info> with public id <info>%s</info> '.
        'and secret <info>%s</info>',
        $client->getName(),
        $client->getPublicId(),
        $client->getSecret()
      )
    );
  }
}
?>
