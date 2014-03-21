<?php

namespace Encase;

use Encase\Container;
use Encase\ContainerItem;

class ContainerItemTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
    $this->containerItem = new ContainerItem($this->container);
  }

  function test_it_stores_container() {
    $this->assertEquals($this->container, $this->containerItem->container);
  }

  function test_it_stores_item_key_value_pair() {
    $this->containerItem->store('box', 'red_box');
    $this->assertEquals('box', $this->containerItem->key);
    $this->assertEquals('red_box', $this->containerItem->value);
  }

  function test_it_injects_container_item_objects_using_container() {
    $object = 'red_box';
    $mockContainer = $this->getMock('Container', array('inject'));
    $mockContainer
      ->expects($this->once())
      ->method('inject')
      ->with($this->equalTo($object));

    $this->containerItem->container = $mockContainer;
    $this->containerItem->inject($object);
  }

  function test_it_detects_if_not_reified() {
    $this->assertFalse($this->containerItem->reified());
  }

  function test_it_detects_if_already_reified() {
    $this->containerItem->reifiedValue = 'red_box';
    $this->assertTrue($this->containerItem->reified());
  }

  function test_it_can_reify_objects() {
    $object = 'a_box';
    $this->containerItem->store('box', $object);

    $this->assertTrue($this->containerItem->reify());
    $this->assertEquals('a_box', $this->containerItem->reifiedValue);
  }

  function newRedBox($container) {
    $this->assertEquals($this->container, $container);
    return 'new_red_box';
  }

  function test_it_can_reify_callables() {
    $callable = array($this, 'newRedBox');
    $this->containerItem->store('a_box', $callable);

    $this->assertTrue($this->containerItem->reify());
    $this->assertEquals('new_red_box', $this->containerItem->reifiedValue);
  }

  function test_it_can_reify_closures() {
    $self = $this;
    $callable = function($container) use($self) {
      $self->assertEquals($self->container, $container);
      return 'new_red_box';
    };

    $this->containerItem->store('a_box', $callable);
    $this->assertTrue($this->containerItem->reify());

    $this->assertEquals('new_red_box', $this->containerItem->reifiedValue);
  }

  function test_it_can_fetch_reifiedValue() {
    $this->containerItem->store('coin', array($this, 'newRedBox'));
    $this->containerItem->reify();
    $this->assertEquals('new_red_box', $this->containerItem->fetch());
  }

  function test_it_returns_reified_instance_for_string() {
    $this->containerItem->store('coin', 'one');
    $this->assertEquals('one', $this->containerItem->instance());
  }

  function test_it_returns_reified_instance_for_callable() {
    $this->containerItem->store('box', array($this, 'newRedBox'));
    $this->assertEquals('new_red_box', $this->containerItem->instance());
  }

}

?>
