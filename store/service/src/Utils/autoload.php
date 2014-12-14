<?php
  spl_autoload_register(function ($className) {
    $path = str_replace('\\', '/', $className);
    $file = __DIR__ . '/../' . $path . '.php';
    if (!is_readable($file))
      throw new Exception('Autoload: Cannot read "' . $path . '" file');
    require_once($file);
  });
?>
