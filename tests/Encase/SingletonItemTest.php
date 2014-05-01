<?php

namespace Encase;

use Encase\Container;
use Encase\SingletonItem;

class SingleBox {
  function needs() {
    return array();
  }
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

  function test_it_can_run_singleton_item_initializer_once() {
    $this->singletonItem->store('foo', 'Encase\\SingleBox');

    $initializer = new MockSingletonInitializer();
    $this->singletonItem->initializer = array($initializer, 'run');

    $instance = $this->singletonItem->instance();
    $this->singletonItem->instance();
    $this->singletonItem->instance();

    $this->assertInstanceOf('Encase\\SingleBox', $instance);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(1, $initializer->count);
  }
}

class MockSingletonInitializer {

  public $count = 0;

  function run($object, $container) {
    $this->count++;
    $this->object = $object;
    $this->container = $container;
  }

}
