<?php
namespace b55\Entity;

class i3Workspace {
  protected $containers;
  protected $name;

  public function __construct($name) {
    $this->setName($name);
    $this->containers = array();
    //$this->containers = array(new i3Container());
  }

  public function getContainers() {
    return $this->containers;
  }

  public function setContainers($containers) {
    $this->containers = $containers;
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function save() {
    $containers = array();
    for ($i = 0, $nb = count($this->containers); $i < $nb; ++$i) {
      $containers[] = $this->containers[$i]->save();
    }

    $return = array(
      'name' => $this->name,
      'type' => 'i3Workspace',
      'containers' => $containers
    );

    return $return;
  }

  public function addContainer(i3Container $i3Container) {
    $this->containers[] = $i3Container;
  }
}
