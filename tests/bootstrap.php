<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart

/**
 * @file
 *   Autoload classes so that tests function properly
 */

spl_autoload_register(
  function($class) {
    $file = __DIR__.'/../'.strtr($class, '_', '/').'.php';
    if (file_exists($file)) {
      require $file;
      return true;
    }
  }
);
