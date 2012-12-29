<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

use b55\i3WebManager as i3wm;
use b55\i3Msg as i3msg;

require_once __DIR__ . '/b55/Resources/lib/utils.php';

$app['i3Msg'] = function () {
  return new i3msg\i55Msg();
};

$console = new Application('i3CliManager', '0.1');

$console
  ->register('i3CliManager:start')
  ->setDefinition(array(
      // new InputOption('some-option', null, InputOption::VALUE_NONE, 'Some help'),
  ))
  ->addArgument(
    'config_name',
    InputArgument::REQUIRED
  )
  ->setDescription('Start an i3Config')
  ->setHelp('Usage :<info>php console i3CliManager:start [config_name] --verbose')
  ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
    $config_name = $input->getArgument('config_name');
    $i3wm = new i3wm\i3WebManager(getYamlFilePathFromApp($app));

    if ($i3wm->is_new()) {
      $output->write("\n<error>You don't have a configuration yet, please make one via the web interface
        (there will be an cli interface for that one day…).</error>\n\n");
      return;
    }

    if (in_array($config_name, $i3wm->getConfigsNames())) {
      $i3wm->run($config_name, $app['i3Msg']);
    }
    else {
      $out = '';
      foreach($i3wm->getConfigsNames() as $name) {
        $out .= "  - $name \n";
      }
      $output->write("\n<info>The config « $config_name » doesn't exist please choose one of these :</info>\n $out \n\n");
    }
});

return $console;
