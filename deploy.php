<?php
//
// deploy.php for in  /home/darki/projets/acsilserver/acsilserver
// 
// Made by emmanuel ohland
// Login   <ohland_e@epitech.net>
// 
// Started on  Fri Jan 31 23:20:29 2014 emmanuel ohland
// Last update Fri Jan 31 23:21:01 2014 emmanuel ohland
//

header('text/plain');
print shell_exec('git pull');

?>
