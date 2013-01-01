<?php
namespace b55;

use b55\Entity;
use b55\Entity\i3Config;
use b55\Entity\i3Workspace;

class i3ConfigParser{
  protected $filename;
  protected $workpaces;

  public function __construct($filename) {
    if (is_file($filename) && is_readable($filename)) {
      $this->filename = $filename;
      $this->workspaces = array();
    }
  }

  public function getFilename() {
    return $this->filename;
  }

  public function setFilename($filename) {
    $this->filename = $filename;
  }

  public function parse() {
    $file_handle = fopen($this->filename,  "r");
    while (!feof($file_handle)) {
      $line = fgets($file_handle);
      $matches = array();
      if (preg_match('#move workspace (.+)#i', $line, $matches)) {
        $workspace_name = $matches[1];
        $this->workspaces[] = new i3Workspace($workspace_name);
      }
    }
    fclose($file_handle);
    return $this->workspaces;
  }
}
