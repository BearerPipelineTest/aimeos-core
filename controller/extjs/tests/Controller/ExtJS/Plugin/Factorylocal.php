<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * ExtJS plugin test factory.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Plugin_Factorylocal
	extends Controller_ExtJS_Common_Factory_Base
{
	/**
	 * @param string $name
	 */
	public static function createController( MShop_Context_Item_Iface $context, $name = null, $domainToTest = 'plugin' )
	{
		if( $name === null ) {
			$name = $context->getConfig()->get( 'classes/controller/extjs/plugin/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $name ) );
		}

		$iface = 'Controller_ExtJS_Common_Iface';
		$classname = 'Controller_ExtJS_Plugin_' . $name;

		$manager = self::createControllerBase( $context, $classname, $iface );
		return self::addControllerDecorators( $context, $manager, $domainToTest );
	}
}
