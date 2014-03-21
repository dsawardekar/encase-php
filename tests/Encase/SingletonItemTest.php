<?php

namespace Encase;

use Encase\Container;
use Encase\SingletonItem;

class SingleBox {

}

class SingletonItemTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
    $this->singletonItem = new SingletonItem($this->container);
  }

  function test_it_returns_same_instance_on_every_fetch() {
    $this->singletonItem->store('box', 'Encase\SingleBox');
    $box1 = $this->singletonItem->instance();
    $box2 = $this->singletonItem->instance();

    $this->assertInstanceOf('Encase\SingleBox', $box1);
    $this->assertInstanceOf('Encase\SingleBox', $box2);
    $this->assertSame($box1, $box2);
  }

}
