<?php
//
// deploy.php for in  /home/darki/projets/acsilserver/acsilserver
// 
// Made by emmanuel ohland
// Login   <ohland_e@epitech.net>
// 
// Started on  Fri Jan 31 23:20:29 2014 emmanuel ohland
// Last update Wed Oct 15 10:11:46 2014 emmanuel ohland
//

header('text/plain');
print shell_exec('git pull');
print shell_exec('./sfcmd');

?>
