<?php
namespace b55\i3Msg;

use b55\i3Msg;
use b55\Entity\i3Workspace;
use b55\Entity\i3Client;

class i55Msg implements i3MsgInterface {
  public function goto_workspace(i3Workspace $i3Workspace) {
    $command = $this->get_goto_command($i3Workspace);
    exec($command);
    // Make that configurable.
    sleep(0.1);
  }

  public function open_client(i3Client $i3Client) {
    if ($cmd = $i3Client->getFullCommand()) {
      $cmd = escapeshellcmd($cmd);
      exec('nohup ' . $cmd . ' > /dev/null 2>&1 &');
      // Make that configurable.
      sleep(2);
    }
  }

  public function open_scratchpad(i3Client $i3Client) {
    // TODO: http://faq.i3wm.org/question/954/open-window-as-a-scratchpad/
    return;
  }

  public function set_layout($layout, $workspace = NULL) {
    $cmd = 'i3-msg layout ' . $layout;
    exec($cmd);
    sleep(0.5);
  }

  /* Protected methods */

  protected function get_goto_command(i3Workspace $i3Workspace) {
    return 'i3-msg workspace ' . $i3Workspace->getName() . ';';
  }
}
