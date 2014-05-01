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

  function test_it_can_run_factory_item_initializer_always() {
    $this->factoryItem->store('foo', 'Encase\\Box');

    $initializer = new MockFactoryInitializer();
    $this->factoryItem->initializer = array($initializer, 'run');

    $instance = $this->factoryItem->instance();
    $this->factoryItem->instance();
    $this->factoryItem->instance();

    $this->assertInstanceOf('Encase\\Box', $instance);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(3, $initializer->count);
  }
}

class MockFactoryInitializer {

  public $count = 0;

  function run($object, $container) {
    $this->count++;
    $this->object = $object;
    $this->container = $container;
  }

}
