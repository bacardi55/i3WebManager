Example:
=======
There is two files in this directory (except the readme file).

***i3CliManager.sh***
This little script help me launch the config I need directly via dmenu (you need to add this script in your $PATH and make it executable to do so).

***i3config.yml***
The i3config.yml file is an config file example (my own config actualy).
I have two configurations:

my work configuration (10 workspaces) :
```
workspaces:
  1: www
    default_layout: tabbed
    clients:
      - Firefox
      - Chormium
  2: dev
    default_layout: tabbed
    clients:
      - tmuxinator capcom #open a tmux connection for my work project (see tmuxinator on github, great app)
  3: mail
    default_layout: stacking
    clients:
      - mutt_perso # mutt with my perso muttrc
      - mutt_work # mutt with my work muttrc
  4: irc
    default_layout: stacking
    clients:
      - ssh kim # ssh connection to my server running irssi
  5: misc
  6: misc
  7: misc
  8: misc
  9: misc
  10: im
    clients:
      - skype
      - piding

scratchpads: # doesnt work yet, but the web app part is done
  - tmux tw_cap # tmux that will launch taskwarrior
```

My home configuration:
```
  1: www
    default_layout: tabbed
    clients:
      - Firefox
  2: dev
    default_layout: tabbed
    clients:
      - tmux
  3: mail
    default_layout: stacking
    clients:
      - mutt_perso # mutt with my perso muttrc
  4: irc
    default_layout: stacking
    clients:
      - ssh kim # ssh connection to my server running irssi
  5: misc
  6: misc
  7: misc
  8: misc
  9: misc
  10: im
    clients:
      - piding
```
