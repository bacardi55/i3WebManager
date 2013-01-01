<?php
namespace b55\i3WebManager;

use Symfony\Component\Yaml\Yaml;

use b55\Entity\i3Config;
use b55\Entity\i3Workspace;
use b55\Entity\i3Container;
use b55\Entity\i3Client;
use b55\Entity\i3Configuration as i3Configuration;
use b55\i3Msg as i3msg;

class i3WebManager {
  protected $file;
  protected $configs;
  protected $plain_config;
  protected $is_loaded = false;
  protected $configuration;

  public function __construct($file, $load = true) {
    $this->file = $file;
    $this->configs = array();
    $this->default_workspaces = array();
    $this->configuration = new i3Configuration();

    if (file_exists($file)) {
      $this->plain_config = Yaml::parse($file);
      //echo '<pre>'; print_r($this->plain_config); die;
    }

    if ($load == true) {
      $this->load();
    }
  }

  /* Configs */
  public function getConfigs($config_name = NULL) {
    if (!$this->is_loaded) {
      $this->load();
    }
    $ret = $this->configs;

    if ($config_name) {
      for ($i = 0, $nb = count($this->configs); $i < $nb; ++$i) {
        if (strcmp($this->configs[$i]->getName(), $config_name) === 0) {
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
    $this->save();
  }

  public function removeConfig($config_name) {
    foreach ($this->configs as $key => $config) {
      if (strcmp($config->getName(), $config_name) === 0) {
        unset($this->configs[$key]);
      }
    }
    $this->configs = array_merge($this->configs);
    $this->save();
  }

  /* Clients */
  public function addClient($config_name, $workspace_name, i3Client $i3Client, $container_name = NULL) {
    $flag = false;
    foreach ($this->configs as $config) {
      if (strcmp($config->getName(), $config_name) === 0) {
        $config->addClient($workspace_name, $i3Client, $container_name);
        $this->save();
      }
    }
  }

  public function removeClient($config_name, $workspace_name, $client_name) {
    if ($config = $this->getConfigs($config_name)) {
      $config->removeClient($workspace_name, $client_name);
      $this->save();
    }
  }

  /* Workspaces */
  public function setWorkspace($config_name, i3Workspace $i3Workspace, $workspace_to_replace = NULL) {
    $flag = false;
    foreach ($this->configs as $config) {
      if (strcmp($config->getName(), $config_name) === 0) {
        if ($workspace_to_replace !== NULL) {
          $nb_workspace = count($config->getWorkspaces());
          $i = 0;
          foreach ($config->getWorkspaces() as $workspace) {
            ++$i;
            if (strcmp($workspace->getName(), $workspace_to_replace) === 0) {
              $workspace->setName($i3Workspace->getName());
              $flag = true;
            }
          }
        }

        if (($workspace_to_replace === NULL)|| ($i == $nb_workspace && !$flag)) {
          $config->addWorkspace($i3Workspace);
        }
      }
    }
    $this->save();
  }

  public function removeWorkspace($config_name, $workspace_name) {
    if ($config = $this->getConfigs($config_name)) {
      $config->removeWorkspace($workspace_name);
      $this->save();
      return;
    }
  }

  /* Configuration */
  public function getConfiguration() {
    return $this->configuration;
  }

  public function setConfiguration(i3Configuration $i3Configuration) {
    $this->configuration = $i3Configuration;
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

  /**
   * Save method
   */
  public function save($real_save = false) {
    $yaml = $this->generateYaml();
    $filename = $this->file;

    if (false === file_put_contents($filename, utf8_encode($yaml), LOCK_EX)) {
      die('Error saving the file, make sure that a file can be created in the folder src/b55/Resources');
    }
    $this->plain_config = Yaml::parse($filename);
  }

  /**
   * Load method
   */
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
          $workspace->setDefaultLayout($workspaces[$i]['default_layout']);

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

      if (array_key_exists('scratchpads', $config)) {
        $scratchpads = $config['scratchpads'];
        for($i = 0, $nb = count($scratchpads); $i < $nb; ++$i) {
          $i3Client = new i3Client($scratchpads[$i]['name']);
          if (array_key_exists('command', $scratchpads[$i])) {
            $i3Client->setCommand($scratchpads[$i]['command']);
          }
          if (array_key_exists('arguments', $scratchpads[$i])) {
            $i3Client->setArguments($scratchpads[$i]['arguments']);
          }
          $i3Config->addScratchpad($i3Client);
        }
      }

      $this->configs[] = $i3Config;
    }

    if (array_key_exists('configuration', $this->plain_config)) {
      $i3Configuration = new i3Configuration();
      if (array_key_exists('default_workspaces', $this->plain_config['configuration'])) {
        $d_workspaces = $this->plain_config['configuration']['default_workspaces'];
        for ($i = 0, $nb = count($d_workspaces); $i < $nb; $i++) {
          $i3Workspace = new i3Workspace($d_workspaces[$i]['name']);
          $i3Configuration->addDefaultWorkspace($i3Workspace);
        }
      }
      $this->configuration = $i3Configuration;
    }

    $this->is_loaded = true;
  }

  /**
   * Run method
   */
  public function run($name, i3Msg\i3MsgInterface $i3Msg) {
    $config = $this->getConfigs($name);

    $workspaces = $config->getWorkspaces();
    foreach ($workspaces as $wk_id => $workspace) {
      $i3Msg->goto_workspace($workspace);

      if ($workspace->getDefaultLayout() != 'default') {
        $i3Msg->set_layout($workspace->getDefaultLayout());
      }

      $containers = $workspace->getContainers();
      foreach ($containers as $ct_id => $container) {
        $clients = $container->getClients();
        foreach ($clients as $client) {
          $i3Msg->open_client($client);
        }
      }
    }
    $scratchpads = $config->getScratchpads();
    foreach ($scratchpads as $sc_id => $scratchpad) {
      $i3Msg->open_scratchpad($scratchpad);
    }
  }

  /* Private Methods */
  /**
   * Generate yaml
   */
  private function generateYaml() {
    $configs = array();
    for ($i = 0, $nb = count($this->configs); $i < $nb; ++$i) {
      $configs[$this->configs[$i]->getName()] = $this->configs[$i]->save();
    }
    $workspaces = array();
    for ($i = 0, $nb = count($this->default_workspaces); $i < $nb; ++$i) {
      $workspaces[$this->default_workspaces[$i]->getName()]
        = $this->default_workspaces[$i]->save();
    }

    $configs = array(
      'i3Config' => $configs,
      'type' => 'i3Config',
      'configuration' => $this->configuration->save(),
    );
    $yaml = Yaml::dump($configs);
    return $yaml;
  }
}
