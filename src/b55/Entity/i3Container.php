<?php
namespace b55\Entity;

use b55\Entity\i3Client as i3Client;

class i3Container {
  protected $clients;
  protected $containers;
  protected $layout;

  public function __construct($layout = NULL) {
    if ($layout) {
      $this->setLayout($layout);
    }
    $this->clients = array();
    $this->containers = array();
  }

  public function getClients($client_name = NULL) {
    $ret = $this->clients;
    if ($client_name && is_string($client_name)) {
      foreach ($this->clients as $client) {
        if (strcmp($client->getName(), $client_name) === 0) {
          $ret = $client;
        }
      }
    }
    return $ret;
  }

  public function setClients($clients) {
    $this->clients = $clients;
  }

  public function getContainers() {
    return $this->containers;
  }

  public function setContainers($containers) {
    $this->containers = $containers;
  }

  public function getLayout() {
    return $this->layout;
  }

  public function setLayout($layout) {
    $this->layout = $clients;
  }

  public function save() {
    $clients = array();
    for ($i = 0, $nb = count($this->clients); $i < $nb; ++$i) {
      $clients[] = $this->clients[$i]->save();
    }

    $containers = array();
    for ($i = 0, $nb = count($this->containers); $i < $nb; ++$i) {
      $containers[] = $this->containers[$i]->save();
    }

    $return = array(
      'name' => $this->layout,
      'type' => 'i3Container',
      'clients' => $clients,
      'containers' => $containers,
    );

    return $return;
  }

  public function addContainer(i3Container $i3Container) {
    $this->containers[] = $i3Container;
  }

  public function addClient(i3Client $i3Client) {
    $this->clients[] = $i3Client;
  }

  public function removeClient($client_name) {
    foreach ($this->clients as $key => $client) {
      if (strcmp($client->getName(), $client_name) === 0) {
        unset($this->clients[$key]);
      }
    }
    $this->clients = array_merge($this->clients);
  }
}
