<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Plugin_FactoryTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/common/decorators/default', array() );
	}


	public function testCreateController()
	{
		$obj = Controller_ExtJS_Plugin_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( 'Controller_ExtJS_Iface', $obj );
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factory::createController( TestHelper::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factory::createController( TestHelper::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factory::createController( TestHelper::getContext(), 'Factory' );
	}


	public function testAbstractAddControllerDecoratorsWithExclude()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', array( 'Example' ) );
		$config->set( 'controller/extjs/plugin/decorators/excludes', array( 'Example' ) );

		$controller = Controller_ExtJS_Plugin_Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( 'Controller_ExtJS_Common_Iface', $controller );
	}


	// using Factorylocal class
	public function testAbstractAddControllerDecoratorsNoDomainException()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factorylocal::createController( TestHelper::getContext(), 'Standard', '' );
	}


	public function testAbstractAddDecorators()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', array( 'Example', 'Example' ) );
		$config->set( 'controller/extjs/plugin/decorators/excludes', array() );

		$controller = Controller_ExtJS_Plugin_Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( 'Controller_ExtJS_Common_Decorator_Iface', $controller );
	}


	public function testAbstractAddDecoratorsExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/extjs/common/decorators/default', array( '$$' ) );

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factorylocal::createController( $context, 'Standard', 'plugin' );
	}


	public function testAbstractAddDecoratorsExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/extjs/common/decorators/default', array( 'WrongClass' ) );

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Plugin_Factorylocal::createController( $context, 'Standard', 'plugin' );
	}

}
