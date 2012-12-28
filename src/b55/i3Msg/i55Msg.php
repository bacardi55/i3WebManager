<?php
namespace b55\i3Msg;

use b55\i3Msg;
use b55\Entity\i3Workspace;
use b55\Entity\i3Client;

class i55Msg implements i3MsgInterface {
  public function goto_workspace(i3Workspace $i3Workspace) {
    $command = $this->generate_goto_command($i3Workspace);
    exec($command);
    // Make that configurable.
    sleep(1);
  }

  public function open_client(i3Client $i3Client) {
    exec($i3Client->getCommand());
    // Make that configurable.
    sleep(3);
  }

  protected function generate_goto_command(i3Workspace $i3Workspace) {
    return 'i3-msg workspace ' . $i3Workspace->getName() . ';';
  }
}
