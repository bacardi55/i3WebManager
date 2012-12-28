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
        if ($workspace->getName() == $name) {
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
      if ($workspace_name == $workspace->getName()) {
        $workspace->removeClient($client_name);
      }
    }
  }

  public function removeWorkspace($workspace_name) {
    foreach ($this->workspaces as $key => $workspace) {
      if ($workspace_name == $workspace->getName()) {
        unset($this->workspaces[$key]);
      }
    }
  }
}
