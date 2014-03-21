<?php

namespace Encase;

use Encase\ContainerItem;

class ObjectItem extends ContainerItem {
  public $injected = false;

  function inject($object, $origin = null) {
    if ($this->injected) {
      return false;
    } else {
      parent::inject($object, $origin);
      $this->injected = true;
      return true;
    }
  }
}

?>
