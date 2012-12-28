<?php
namespace b55\i3Msg;

use b55\Entity\i3Workspace;
use b55\Entity\i3Client;

interface i3MsgInterface {
  public function goto_workspace(i3Workspace $i3Workspace);
  public function open_client(i3Client $i3Client);
}
