<?php

namespace Encase;

use Encase\Container;
use Encase\FactoryItem;

class Box {

}

class FactoryItemTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
    $this->factoryItem = new FactoryItem($this->container);
  }

  function test_it_creates_new_instance_on_fetch() {
    $this->factoryItem->store('box', 'Encase\Box');
    $box = $this->factoryItem->instance();

    $this->assertInstanceOf('Encase\Box', $box);
  }

  function test_it_creates_new_instance_on_each_fetch() {
    $this->factoryItem->store('box', 'Encase\Box');
    $box = $this->factoryItem->instance();
    $box2 = $this->factoryItem->instance();

    $this->assertInstanceOf('Encase\Box', $box);
    $this->assertInstanceOf('Encase\Box', $box2);

    $this->assertNotSame($box, $box2);
  }
}
