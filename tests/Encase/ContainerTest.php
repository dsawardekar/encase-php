<?php

namespace Encase;

use Encase\Container;

class Coin {

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

}

?>
