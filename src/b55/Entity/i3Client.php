<?php
namespace b55\Entity;

class i3Client {
  protected $name;
  protected $arguments;

  public function __construct($name, $arguments = NULL) {
    $this->setName($name);

    if ($arguments) {
      $this->setArguments($arguments);
    }
  }

  public function getName() {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
  }

  public function getArguments() {
    return $this->arguments;
  }

  public function setArguments($arguments) {
    $this->arguments = $arguments;
  }

  public function save() {
    $return = array(
      'name' => $this->name,
      'argument' => $this->arguments,
      'type' => 'i3Client',
    );

    return $return;
  }
}
