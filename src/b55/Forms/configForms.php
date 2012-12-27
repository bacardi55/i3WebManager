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
}
