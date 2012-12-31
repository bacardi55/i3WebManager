Example:
=======
The i3config.yml file is an config file example (my own config actualy).
I have two configurations:

my work configuration (10 workspaces) :
```
workspaces:
  1: www
    - Firefox
    - Chormium
  2: dev
    - tmuxinator capcom #open a tmux connection for my work project (see tmuxinator on github, great app)
  3: mail
    - mutt_perso # mutt with my perso muttrc
    - mutt_work # mutt with my work muttrc
  4: irc
    - ssh kim # ssh connection to my server running irssi
  5: misc
  6: misc
  7: misc
  8: misc
  9: misc
  10: im
    - skype
    - piding

scratchpads: # doesn't work yet, but the web app part is done
  - tmux tw_cap # tmux that will launch taskwarrior
```

My home configuration:
```
  1: www
    - Firefox
  2: dev
    - tmux
  3: mail
    - mutt_perso # mutt with my perso muttrc
  4: irc
    - ssh kim # ssh connection to my server running irssi
  5: misc
  6: misc
  7: misc
  8: misc
  9: misc
  10: im
    - piding
```
