<?php
// src/AcsilServer/APIBundle/Command/ClientPopulateCommand.php
namespace AcsilServer\APIBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use AcsilServer\APIBundle\Entity\Client;

class ClientPopulateCommand extends ContainerAwareCommand
{
  protected function configure()
  {
    $this
      ->setName('acsilserver:api:client:populate')
      ->setDescription('Populate database with default OAuth clients')
      ->setHelp(
        'The <info>%command.name%</info> command creates default clients.'
      );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
    $client = new Client();
    $client->setName('Dev Mobile Client');
    $client->setRandomId('powyjhqgq28scskw0w04wg8wck8osksgko0ggwgk44kokwo8k');
    $client->setSecret('29zjq3ov25hccgk48k84swwo800gccoo08wk40sw48s00gc8kw');
    $client->setAllowedGrantTypes(array('password'));
    $clientManager->updateClient($client);
  }
}
?>
