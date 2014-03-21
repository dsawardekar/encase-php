<?php

namespace Encase;

use Encase\Container;
use Encase\ContainerItem;

class SingletonItem extends ContainerItem {

  public $singleton = null;

  function fetch() {
    if (is_null($this->singleton)) {
      $this->singleton = new $this->reifiedValue;
    }

    return $this->singleton;
  }
}

?>
