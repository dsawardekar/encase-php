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

  function test_it_can_run_initializer_once() {
    $this->objectItem->store('foo', 'value');

    $initializer = new MockInitializer();
    $this->objectItem->initializer = array($initializer, 'run');
    $instance = $this->objectItem->instance();
    $this->objectItem->instance();
    $this->objectItem->instance();

    $this->assertEquals('value', $instance);
    $this->assertEquals('value', $initializer->object);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(1, $initializer->count);
  }

}

class MockInitializer {

  public $count = 0;

  function run($object, $container) {
    $this->count++;
    $this->object = $object;
    $this->container = $container;
  }

}

?>
