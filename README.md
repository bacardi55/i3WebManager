i3WebManager
============

Summary
=======
i3WebManager has a simple purpose. Help you create the config you want when starting X.

Description
===========
i3wm let you launch software when it start. Plus it let you assign window to workspace.
My need everyvmorning at work is more complex. I want to open specific client in a specific state in a specific layout. Plus, I have a lot of trouble to do it in the config file.

I could have done a bash script with i3msg to do the same but I wanted an easy way to modify my configuration and I wanted a way to handle more than one configuration. I don't open the same app in the same way at work or at home (you could even have a configuration for «quick start» with only a web browser).

I like to release early, release often so be carreful when trying it :D.


Install
=======
To install, just do :
```bash
git clone https://github.com/bacardi55/i3WebManager/
curl -s http://getcomposer.org/installer | php
php composer.phar install
```

Current State
=============
Tag 0.1 : 
The app is currently useless. The load/saving part work though and that's the most important part.

The 0.2 will have a full working process to let you create a simple configuration to load client in workspace (no layout, split, container yet).

Wish list
=========
What I want from this app (at least) :
- First :
  - Create multiple config (home, offices, …)
  - Add client per workspace
  - Add client in specific split / layout
  - A cli php script to add at the start of i3 to launch your choosen configuration
- Then :
  - Pre-configure i3WebManager by reading i3/config file
  - Drag & drop UI to create your i3 session as wanted
  - An export of the configuration in a bash script to not have php installed on the user pc.

