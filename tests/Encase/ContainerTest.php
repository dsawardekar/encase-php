<?php

namespace Encase;

use Encase\Container;

class Coin {
  function needs() {
    return array();
  }
}

class ContainerTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
  }

  function test_it_can_be_created_without_parent() {
    $this->assertNull($this->container->parent);
  }

  function test_it_can_store_parent() {
    $parent = $this->container;
    $container = new Container($parent);

    $this->assertSame($parent, $container->parent);
  }

  function test_it_has_an_item_factory() {
    $this->assertInstanceOf('Encase\ContainerItemFactory', $this->container->itemFactory());
  }

  function test_it_can_lookup_items_from_item_factory() {
    $objectItem = $this->container->itemFor('object');
    $this->assertInstanceOf('Encase\ObjectItem', $objectItem);
  }

  function test_it_can_register_container_items() {
    $this->container->register('object', 'coin', 'one');
    $this->assertTrue($this->container->contains('coin'));
  }

  function test_it_can_unregister_container_items() {
    $this->container->register('object', 'coin', 'one');
    $this->container->unregister('coin');
    $this->assertFalse($this->container->contains('coin'));
  }

  function test_it_can_clear_all_container_items() {
    $this->container->register('object', 'coin', 'one');
    $this->container->register('object', 'box', 'red');
    $this->container->clear();

    $this->assertFalse($this->container->contains('coin'));
    $this->assertFalse($this->container->contains('box'));
  }

  function test_it_can_store_objects() {
    $this->container->register('object', 'coin', 'one');
    $this->assertEquals('one', $this->container->lookup('coin'));
  }

  function test_it_can_store_factories() {
    $this->container->register('factory', 'coin', 'Encase\Coin');
    $this->assertInstanceOf('Encase\Coin', $this->container->lookup('coin'));
  }

  function test_it_can_store_singletons() {
    $this->container->register('singleton', 'single_coin', 'Encase\Coin');
    $coin1 = $this->container->lookup('single_coin');
    $coin2 = $this->container->lookup('single_coin');

    $this->assertSame($coin1, $coin2);
  }

  function test_it_throws_an_exception_for_missing_container_items() {
    $this->setExpectedException('\RuntimeException');
    $this->container->lookup('some_unknown_key');
  }

  function test_it_can_create_child_containers() {
    $child = $this->container->child();
    $this->assertSame($this->container, $child->parent);
  }

  function test_it_can_lookup_value_in_parent_container_from_child_container() {
    $this->container->object('coin', 'two');
    $child = $this->container->child();

    $this->assertEquals('two', $child->lookup('coin'));
  }

  function test_it_can_lookup_value_in_child_container() {
    $child = $this->container->child();
    $child->register('object', 'coin', 'three');

    $this->assertEquals('three', $child->lookup('coin'));
  }

  function test_it_can_override_value_in_parent_container() {
    $this->container->object('coin', 'four');
    $child = $this->container->child();
    $child->object('coin', 'four');

    $this->assertEquals('four', $child->lookup('coin'));
  }

  function test_it_can_use_initializer_with_objects() {
    $initializer = new MockIntegrationInitializer();
    $this->container->object('foo', 'bar');
    $this->container->initializer('foo', array($initializer, 'run'));

    $instance = $this->container->lookup('foo');
    $this->container->lookup('foo');

    $this->assertEquals('bar', $initializer->object);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(1, $initializer->count);
  }

  function test_it_can_use_initializer_with_factory_items() {
    $initializer = new MockIntegrationInitializer();
    $this->container->factory('foo', 'Encase\Coin');
    $this->container->initializer('foo', array($initializer, 'run'));

    $instance = $this->container->lookup('foo');
    $this->container->lookup('foo');

    $this->assertInstanceOf('Encase\Coin', $initializer->object);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(2, $initializer->count);
  }

  function test_it_can_use_initializer_with_singleton() {
    $initializer = new MockIntegrationInitializer();
    $this->container->singleton('foo', 'Encase\Coin');
    $this->container->initializer('foo', array($initializer, 'run'));

    $instance = $this->container->lookup('foo');
    $this->container->lookup('foo');

    $this->assertInstanceOf('Encase\Coin', $initializer->object);
    $this->assertEquals($this->container, $initializer->container);
    $this->assertEquals(1, $initializer->count);
  }

  function test_it_allows_package_to_add_items_to_container() {
    $this->container->packager('myPackager', 'Encase\MyPackager');

    $this->assertEquals('aValue', $this->container->lookup('a'));
    $this->assertEquals('bValue', $this->container->lookup('b'));
    $this->assertEquals('cValue', $this->container->lookup('c'));
  }
}

class MyPackager {

  function onInject($container) {
    $container
      ->object('a', 'aValue')
      ->object('b', 'bValue')
      ->object('c', 'cValue');
  }

}

class MockIntegrationInitializer {

  public $count = 0;

  function run($object, $container) {
    $this->count++;
    $this->object = $object;
    $this->container = $container;
  }

}

