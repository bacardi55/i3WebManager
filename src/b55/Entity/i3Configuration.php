<?php
namespace b55\Entity;

class i3Configuration {
  protected $default_workspaces;

  public function __construct() {
    $this->default_workspaces= array();
  }

  public function getDefaultWorkspaces() {
    return $this->default_workspaces;
  }

  public function setDefaultWorkspaces($workspaces) {
    for ($i = 0, $nb = count($workspaces); $i < $nb; ++$i) {
      if ($workspaces[$i] instanceof i3Workspace) {
        $this->default_workspaces[] = $workspaces[$i];
      }
    }
  }

  public function addDefaultWorkspace(i3Workspace $i3Workspace) {
    $this->default_workspaces[] = $i3Workspace;
  }

  public function save() {
    $df = array();
    foreach ($this->default_workspaces as $wk) {
      $df[] = $wk->save();
    }

    return array(
      'default_workspaces' => $df
    );
  }
}
