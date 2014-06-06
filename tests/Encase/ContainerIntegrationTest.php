<?php

namespace Encase;

use Encase\Container;
use Encase\INeeds;

class Apple {};
class Banana {};
class Mango {};

class FruitBox {

  function needs() {
    return array('apple', 'banana', 'mango');
  }

  function onInject($container) {
    $this->injected = true;
  }

}

class FruitBoxWithNeeds implements INeeds {

  function needs() {
    return array('apple', 'banana', 'mango');
  }

  function onInject($container) {
    $this->injected = true;
  }

}

class Crate {

  function needs() {
    return array('fruit_box', 'fruit_box_with_needs');
  }

  function onInject($container) {
    $this->injected = true;
  }

}

class Application {

  function needs() {
    return array('remote_service', 'api_key');
  }

}

class RemoteService {};
class LocalService {};

class ContainerIntegrationTest extends \PHPUnit_Framework_TestCase {

  function test_it_can_encase_a_fruit_box() {
    $container = new Container();
    $container->factory('apple', 'Encase\Apple')
              ->factory('banana', 'Encase\Banana')
              ->factory('mango', 'Encase\Mango')
              ->factory('fruit_box', 'Encase\FruitBox');


    $box = $container->lookup('fruit_box');

    $this->assertInstanceOf('Encase\FruitBox', $box);
    $this->assertInstanceOf('Encase\Apple', $box->apple);
    $this->assertInstanceOf('Encase\Banana', $box->banana);
    $this->assertInstanceOf('Encase\Mango', $box->mango);
    $this->assertSame($container, $box->container);
    $this->assertTrue($box->injected);
  }

  function test_it_can_encase_a_fruit_box_with_needs() {
    $container = new Container();
    $container->factory('apple', 'Encase\Apple')
              ->factory('banana', 'Encase\Banana')
              ->factory('mango', 'Encase\Mango')
              ->factory('fruit_box', 'Encase\FruitBoxWithNeeds');


    $box = $container->lookup('fruit_box');

    $this->assertInstanceOf('Encase\FruitBoxWithNeeds', $box);
    $this->assertInstanceOf('Encase\Apple', $box->apple);
    $this->assertInstanceOf('Encase\Banana', $box->banana);
    $this->assertInstanceOf('Encase\Mango', $box->mango);
    $this->assertSame($container, $box->container);
    $this->assertTrue($box->injected);
  }

  function test_it_can_encase_a_crate_of_fruit_boxes() {
    $container = new Container();
    $container->factory('apple', 'Encase\Apple')
              ->factory('banana', 'Encase\Banana')
              ->factory('mango', 'Encase\Mango')
              ->factory('fruit_box', 'Encase\FruitBox')
              ->factory('fruit_box_with_needs', 'Encase\FruitBoxWithNeeds')
              ->object('crate', new Crate());

    $crate = $container->lookup('crate');
    $box = $crate->fruit_box;
    $box_with_needs = $crate->fruit_box_with_needs;

    // crate
    $this->assertInstanceOf('Encase\Crate', $crate);
    $this->assertSame($container, $crate->container);
    $this->assertTrue($crate->injected);

    // fruit_box
    $this->assertInstanceOf('Encase\FruitBox', $box);
    $this->assertInstanceOf('Encase\Apple', $box->apple);
    $this->assertInstanceOf('Encase\Banana', $box->banana);
    $this->assertInstanceOf('Encase\Mango', $box->mango);
    $this->assertSame($container, $box->container);
    $this->assertTrue($box->injected);

    // fruit_box_with_needs
    $this->assertInstanceOf('Encase\FruitBoxWithNeeds', $box_with_needs);
    $this->assertInstanceOf('Encase\Apple', $box_with_needs->apple);
    $this->assertInstanceOf('Encase\Banana', $box_with_needs->banana);
    $this->assertInstanceOf('Encase\Mango', $box_with_needs->mango);
    $this->assertSame($container, $box_with_needs->container);
    $this->assertTrue($box_with_needs->injected);
  }

  function test_it_can_encase_application_with_child_container_for_tests() {
    $container = new Container();
    $container->factory('app', 'Encase\Application');
    $container->factory('remote_service', 'Encase\RemoteService');
    $container->object('api_key', 'foobar');

    $child = $container->child();
    $child->factory('remote_service', 'Encase\LocalService');

    $service = $child->lookup('remote_service');
    $this->assertInstanceOf('Encase\LocalService', $service);
    $this->assertEquals('foobar', $child->lookup('app')->api_key);
  }

  function test_it_can_encase_application_with_lazy_dependencies() {
    $container = new Container();
    $container->object('api_key', 'foobar');
    $container->factory('app', function($container) {
      return 'Encase\Application';
    });

    $container->object('remote_service', function($container) {
      return new RemoteService();
    });

    $app = $container->lookup('app');
    $this->assertInstanceOf('Encase\RemoteService', $app->remote_service);
    $this->assertEquals('foobar', $app->api_key);
  }

  function test_nicops_case() {
    $container = new Container();
    $container->factory('house', 'Encase\House');
    $container->object('logger', new Logger());

    $child = $container->child();
    $child->object('logger', new MagicLogger());

    $house = $child->lookup('house');
    $logger = $house->logger;

    $this->assertInstanceOf('Encase\MagicLogger', $logger);
  }
}

class House {
  function needs() {
    return array('logger');
  }
}

class Logger {};
class MagicLogger {};

?>
