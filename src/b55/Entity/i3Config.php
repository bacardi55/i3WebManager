<?php
namespace b55\Entity;

class i3Config {
  private $name;
  private $workspaces;

  /**
   * Constructor.
   */
  public function __construct ($name, $nb_workspaces = 0) {
    $this->name = $name;
    $this->workspaces = array();

    if ($nb_workspaces) {
      for ($i = 0; $i < $nb_workspaces; ++$i) {
        $this->workspaces[] = new i3Workspace($i);
      }
    }
  }

  /**
   * Getters/Setters
   */
  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getWorkspaces($name = NULL) {
    $ret = $this->workspaces;

    if ($name != NULL) {
      foreach ($this->workspaces as $workspace) {
        if (strcmp($workspace->getName(), $name) === 0) {
          return $workspace;
        }
      }
    }

    return $this->workspaces;
  }

  public function setWorkspaces($workspaces) {
    $this->workspaces = $workspaces;
  }

  public function save() {
    $return = array(
      'name' => $this->name,
      'type' => 'i3Config',
      'workspaces' => array()
    );

    for ($i = 0, $nb = count($this->workspaces); $i < $nb; ++$i) {
      $return['workspaces'][] = $this->workspaces[$i]->save();
    }

    return $return;
  }

  public function addWorkspace(i3Workspace $i3Workspace) {
    $this->workspaces[] = $i3Workspace;
  }

  public function removeClient($workspace_name, $client_name) {
    foreach ($this->workspaces as $workspace) {
      if (strcmp($workspace_name, $workspace->getName()) === 0) {
        $workspace->removeClient($client_name);
      }
    }
  }

  public function removeWorkspace($workspace_name) {
    foreach ($this->workspaces as $key => $workspace) {
      if (strcmp($workspace_name, $workspace->getName()) === 0) {
        unset($this->workspaces[$key]);
      }
    }
    $this->workspaces = array_merge($this->workspaces);
  }

  public function addClient($workspace_name, i3Client $i3Client, $container_name = NULL) {
    foreach ($this->workspaces as $workspace) {
      if (strcmp($workspace->getName(), $workspace_name) === 0) {
        $workspace->addClient($i3Client, $container_name);
      }
    }
  }
}
