<?php
namespace b55\Entity;

use b55\Entity\i3Client as i3Client;

class i3Workspace {
  protected $containers;
  protected $name;

  public function __construct($name) {
    $this->setName($name);
    $this->containers = array();
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

  public function getNumberOfClients() {
    $nb = 0;
    foreach ($this->containers as $container) {
      $nb += count($container->getClients());
    }

    return $nb;
  }

  public function getClientsNames() {
    $ret = '';
    foreach ($this->containers as $container) {
      foreach ($container->getClients() as $client) {
        $ret .= $client->getName() . ', ';
      }
    }
    return substr($ret, 0, -2);
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

  public function getClient($client_name) {
    foreach ($this->containers as $container) {
      $i3Client = $container->getClients($client_name);

      if ($i3Client instanceof i3Client) {
        return $i3Client;
      }
    }
    return false;
  }

  public function removeClient($client_name) {
    foreach ($this->getContainers() as $container) {
      $container->removeClient($client_name);
    }
  }
}
