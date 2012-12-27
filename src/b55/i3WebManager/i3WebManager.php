<?php
namespace b55\i3WebManager;

use Symfony\Component\Yaml\Yaml;

use b55\Entity\i3Config;
use b55\Entity\i3Workspace;
use b55\Entity\i3Container;
use b55\Entity\i3Client;

class i3WebManager {
  protected $configs;

  public function __construct($file) {
    $this->configs = array();

    if (file_exists($file)) {
      $array = Yaml::parse($file);
      $i3Configs = $array['i3Config'];

      $this->load($i3Configs);

      $this->save();
      echo '<pre>';print_r($this);die;
    }
  }

  public function getConfigs($config_name = NULL) {
    if (!$config_name);
    return $this->configs;
  }

  public function setConfigs($configs) {
    $this->configs = $configs;
  }

  public function addConfig($config_name, $nb_workspaces = 0) {
    $this->configs[] = new i3Config($config_name, $nb_workspaces);
  }

  /**
   * Return if the config exists or not
   *
   * @return Boolean
   *   False if the config existed
   *   True if the config doen't exists
   */
  public function is_new() {
    if (!count($this->configs)) {
      return true;
    }
    return false;
  }

  public function save($filename) {
    $this->generateYaml();

    // TODO: Save file.
    echo '<pre>';
    print_r($configs);
    print_r($yaml);
    file_put_contents($filename, utf8_encode($yaml), LOCK_EX);
    die;
  }

  public function load($configs) {
    foreach ($configs as $name => $config) {
      $i3Config = new i3Config($name);
      if (array_key_exists('workspaces', $config)) {
        $workspaces = $config['workspaces'];
        for ($i = 0, $nb = count($workspaces); $i < $nb; ++$i) {
          $workspace = new i3Workspace($workspaces[$i]['name']);

          if (array_key_exists('containers', $workspace)) {
            $containers = $workspace['containers'];
            for ($j = 0, $nbc = count($containers); $j < $nb; ++$j) {
              $container = new i3Container($containers[$j]['name']);

              if (array_key_exists('clients', $containers)) {
                $clients = $containers['client'];
                for ($k = 0, $nbcl = count($clients); $k < $nbcl; ++$k) {
                  $client = new i3Client($clients[$k]['name']);
                  $container->addClient($client);
                }
              }
              $workspace->addContainer($container);
            }
          }
          $i3Config->addWorkspace($workspace);
        }
      }
      $this->configs[] = $i3Config;
    }
  }

  private function generateYaml() {
    $configs = array();
    for ($i = 0, $nb = count($this->configs); $i < $nb; ++$i) {
      $configs[$this->configs[$i]->getName()] = $this->configs[$i]->save();
    }

    $configs = array('i3Config' => $configs, 'type' => 'i3Config', 'configuration' => array());

    $yaml = Yaml::dump($configs);
    return $yaml;
  }
}
