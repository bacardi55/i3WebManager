<?php
namespace b55\Forms;

class configForms {
  protected $form_factory;

  public function __construct($form_factory) {
    $this->form_factory = $form_factory;
  }

  public function getAddForm($data = array()) {
    $form = $this->form_factory->createBuilder('form', $data)
      ->add('config_name', 'text')
      ->add('config_nb_workspace', 'integer')
      ->getForm();

    return $form;
  }

  public function getClientForm($data = array()) {
    $form = $this->form_factory->createBuilder('form', $data)
      ->add('name', 'text')
      ->add('command', 'text')
      ->add('arguments', 'text')
      ->getForm();

    return $form;
  }

  public function getWorkspaceForm($data = array()) {
    $form = $this->form_factory->createBuilder('form', $data)
      ->add('name', 'text')
      ->getForm();

    return $form;
  }
}
