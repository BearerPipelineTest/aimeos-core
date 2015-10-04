<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Frontend_Plugin_Decorator_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$controller = Controller_Frontend_Service_Factory::createController( $context, 'Standard' );
		$this->object = new Controller_Frontend_Service_Decorator_Example( $context, $controller );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCall()
	{
		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$this->object->checkServiceAttributes( 'delivery', -1, array() );
	}

}
