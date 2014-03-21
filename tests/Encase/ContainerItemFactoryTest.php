<?php

namespace Encase;

use Encase\Container;
use Encase\ContainerItem;
use Encase\ObjectItem;
use Encase\FactoryItem;
use Encase\SingletonItem;

class ContainerItemFactoryTest extends \PHPUnit_Framework_TestCase {

  function setUp() {
    $this->container = new Container();
    $this->factory = new ContainerItemFactory();
  }

  function test_it_returns_object_item_for_type_object() {
    $this->assertInstanceOf(
      'Encase\ObjectItem',
      $this->factory->build('object', $this->container)
    );
  }

  function test_it_returns_factory_item_for_type_factory() {
    $this->assertInstanceOf(
      'Encase\FactoryItem',
      $this->factory->build('factory', $this->container)
    );
  }

  function test_it_returns_singleton_item_for_type_singleton() {
    $this->assertInstanceOf(
      'Encase\SingletonItem',
      $this->factory->build('singleton', $this->container)
    );
  }

  function test_it_returns_container_item_for_type_custom() {
    $this->assertInstanceOf(
      'Encase\ContainerItem',
      $this->factory->build('custom', $this->container)
    );
  }

}
