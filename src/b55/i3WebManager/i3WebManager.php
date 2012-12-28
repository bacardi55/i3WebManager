<?php
namespace b55\i3WebManager;

use Symfony\Component\Yaml\Yaml;

use b55\Entity\i3Config;
use b55\Entity\i3Workspace;
use b55\Entity\i3Container;
use b55\Entity\i3Client;
use b55\i3Msg as i3msg;

class i3WebManager {
  protected $file;
  protected $configs;
  protected $plain_config;
  protected $is_loaded = false;

  public function __construct($file) {
    $this->file = $file;
    $this->configs = array();

    if (file_exists($file)) {
      $this->plain_config = Yaml::parse($file);
    }
  }

  public function getConfigs($config_name = NULL) {
    if (!$this->is_loaded) {
      $this->load();
    }
    $ret = $this->configs;

    if ($config_name) {
      for ($i = 0, $nb = count($this->configs); $i < $nb; ++$i) {
        if ($this->configs[$i]->getName() == $config_name) {
          $ret = $this->configs[$i];
        }
      }
    }
    return $ret;
  }

  public function setConfigs($configs) {
    $this->configs = $configs;
  }

  public function getconfigsNames() {
    return array_keys($this->plain_config['i3Config']);
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
    if (!$this->is_loaded) {
      $this->load();
    }

    if (!count($this->configs)) {
      return true;
    }
    return false;
  }

  public function setClient($config_name, $workspace_name, i3Client $i3Client, $client_to_replace = NULL) {
    $flag = false;
    foreach ($this->configs as $config) {
      if ($config->getName() == $config_name) {
        foreach ($config->getWorkspaces() as $workspace) {
          if ($workspace->getName() == $workspace_name) {
            $nb_containers = count($workspace->getContainers());
            $i = 0;
            foreach ($workspace->getContainers() as $container) {
              ++$i;
              if ($client_to_replace) {
                foreach ($container->getClients() as $client) {
                  if ($client instanceof i3Client && $client->getName() == $client_to_replace) {
                    $flag = true;
                    $client->setName($i3Client->getName());
                    $client->setCommand($i3Client->getCommand());
                    $client->setArguments($i3Client->getArguments());
                  }
                }
              }

              if (!$client_to_replace || ($i == $nb_containers && !$flag)) {
                $container->addClient($i3Client);
              }
            }
          }
        }
      }
    }
    $this->save();
  }

  public function setWorkspace($config_name, i3Workspace $i3Workspace, $workspace_to_replace = NULL) {
    $flag = false;
    foreach ($this->configs as $config) {
      if ($config->getName() == $config_name) {
        if ($workspace_to_replace) {
          $nb_workspace = count($config->getWorkspaces());
          foreach ($config->getWorkspaces() as $workspace) {
            if ($workspace->getName() == $workspace_to_replace) {
              $workspace->setName($i3Workspace->getName());
              $flag = true;
            }
          }
        }

        if (!$workspace_to_replace || ($i == $nb_workspace && !$flag)) {
                die ('test');
          $config->addWorkspace($i3Workspace);
        }
      }
    }
    $this->save();
  }

  public function save($real_save = false) {
    $yaml = $this->generateYaml();
    $filename = $this->file;
/*
    if (!$real_save) {
      $filename .= '_bak';
    }
*/
    if (false === file_put_contents($filename, utf8_encode($yaml), LOCK_EX)) {
      die('Error saving the file, make sure that a file can be created in the folder src/b55/Resources');
    }
    $this->plain_config = Yaml::parse($filename);
  }

  public function load($config_name = NULL) {
    $configs = $this->plain_config['i3Config'];
    if (!is_array($configs)) {
      return;
    }
    foreach ($configs as $name => $config) {
      $i3Config = new i3Config($name);
      if (array_key_exists('workspaces', $config)) {
        $workspaces = $config['workspaces'];
        for ($i = 0, $nb = count($workspaces); $i < $nb; ++$i) {
          $workspace = new i3Workspace($workspaces[$i]['name']);

          // In the next version, this part will become way more
          // complicated (containers in containers, …).
          if (array_key_exists('containers', $workspaces[$i])) {
            $containers = $workspaces[$i]['containers'];

            for ($j = 0, $nbc = count($containers); $j < $nbc; ++$j) {
              $container = new i3Container($containers[$j]['name']);
              if (array_key_exists('clients', $containers[$j])) {
                $clients = $containers[$j]['clients'];

                for ($k = 0, $nbcl = count($clients); $k < $nbcl; ++$k) {
                  $cl = $clients[$k];
                  $client = new i3Client($cl['name']);
                  if (array_key_exists('command', $cl)) {
                    $client->setCommand($cl['command']);
                  }
                  if (array_key_exists('arguments', $cl)) {
                    $client->setArguments($cl['arguments']);
                  }
                  $container->addClient($client);
                }
                $workspace->addContainer($container);
              }
            }
            $i3Config->addWorkspace($workspace);
          }
        }
      }
      $this->configs[] = $i3Config;
    }
    $this->is_loaded = true;
  }

  public function run($name, i3Msg\i3MsgInterface $i3Msg) {
    $config = $this->getConfigs($name);

    $workspaces = $config->getWorkspaces();
    foreach ($workspaces as $wk_id => $workspace) {
      $i3Msg->goto_workspace($workspace);
      $containers = $workspace->getContainers();
      foreach ($containers as $ct_id => $container) {
        $clients = $container->getClients();
        foreach ($clients as $client) {
          $i3Msg->open_client($client);
        }
      }
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
