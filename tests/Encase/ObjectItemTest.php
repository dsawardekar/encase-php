<?php

namespace Encase;

use Encase\ObjectItem;
use Encase\Container;

class ObjectItemTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
    $this->objectItem = new ObjectItem($this->container);
  }

  function test_it_applies_injection_if_not_injected() {
    $this->assertTrue($this->objectItem->inject('object'));
  }

  function test_it_does_not_apply_injection_if_already_injected() {
    $this->objectItem->inject('object');
    $this->assertFalse($this->objectItem->inject('object'));
  }

}

?>
