<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Default implementation for last seen products.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Catalog_Detail_Seen_Default
	extends Client_Html_Abstract
{
	/** client/html/catalog/detail/seen/default/subparts
	 * List of HTML sub-clients rendered within the catalog detail seen section
	 *
	 * The output of the frontend is composed of the code generated by the HTML
	 * clients. Each HTML client can consist of serveral (or none) sub-clients
	 * that are responsible for rendering certain sub-parts of the output. The
	 * sub-clients can contain HTML clients themselves and therefore a
	 * hierarchical tree of HTML clients is composed. Each HTML client creates
	 * the output that is placed inside the container of its parent.
	 *
	 * At first, always the HTML code generated by the parent is printed, then
	 * the HTML code of its sub-clients. The order of the HTML sub-clients
	 * determines the order of the output of these sub-clients inside the parent
	 * container. If the configured list of clients is
	 *
	 *  array( "subclient1", "subclient2" )
	 *
	 * you can easily change the order of the output by reordering the subparts:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1", "subclient2" )
	 *
	 * You can also remove one or more parts if they shouldn't be rendered:
	 *
	 *  client/html/<clients>/subparts = array( "subclient1" )
	 *
	 * As the clients only generates structural HTML, the layout defined via CSS
	 * should support adding, removing or reordering content by a fluid like
	 * design.
	 *
	 * @param array List of sub-client names
	 * @since 2014.03
	 * @category Developer
	 */
	private $_subPartPath = 'client/html/catalog/detail/seen/default/subparts';
	private $_subPartNames = array();


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string HTML code
	 */
	public function getBody( $uid = '', array &$tags = array(), &$expire = null )
	{
		return '';
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @param array &$tags Result array for the list of tags that are associated to the output
	 * @param string|null &$expire Result variable for the expiration date of the output (null for no expiry)
	 * @return string String including HTML tags for the header
	 */
	public function getHeader( $uid = '', array &$tags = array(), &$expire = null )
	{
		return '';
	}


	/**
	 * Returns the sub-client given by its name.
	 *
	 * @param string $type Name of the client type
	 * @param string|null $name Name of the sub-client (Default if null)
	 * @return Client_Html_Interface Sub-client object
	 */
	public function getSubClient( $type, $name = null )
	{
		return $this->_createSubClient( 'catalog/detail/seen/' . $type, $name );
	}


	/**
	 * Returns the list of sub-client names configured for the client.
	 *
	 * @return array List of HTML client names
	 */
	protected function _getSubClientNames()
	{
		return $this->_getContext()->getConfig()->get( $this->_subPartPath, $this->_subPartNames );
	}


	/**
	 * Processes the input, e.g. store given values.
	 * A view must be available and this method doesn't generate any output
	 * besides setting view variables.
	 */
	public function process()
	{
		if( ( $id = $this->getView()->param( 'd-product-id' ) ) !== null )
		{
			$context = $this->_getContext();
			$session = $context->getSession();
			$str = $session->get( 'arcavias/catalog/session/seen/list' );

			if( ( $lastSeen = @unserialize( $str ) ) === false ) {
				$lastSeen = array();
			}

			if( isset( $lastSeen[$id] ) )
			{
				$html = $lastSeen[$id];
				unset( $lastSeen[$id] );
				$lastSeen[$id] = $html;
			}
			else
			{
				/** client/html/catalog/session/seen/default/maxitems
				 * Maximum number of products displayed in the "last seen" section
				 *
				 * This option limits the number of products that are shown in the
				 * "last seen" section after the user visited their detail pages. It
				 * must be a positive integer value greater than 0.
				 *
				 * @param integer Number of products
				 * @since 2014.03
				 * @category User
				 * @category Developer
				 */
				$max = $this->_getContext()->getConfig()->get( 'client/html/catalog/session/seen/default/maxitems', 6 );

				$lastSeen[$id] = $this->_getHtml( $id );
				$lastSeen = array_slice( $lastSeen, -$max, $max, true );
			}

			$session->set( 'arcavias/catalog/session/seen/list', serialize( $lastSeen ) );

			foreach( $session->get( 'arcavias/catalog/session/seen/cache', array() ) as $key => $value ) {
				$session->set( $key, null );
			}
		}

		parent::process();
	}


	/**
	 * Returns the generated HTML for the given product ID.
	 *
	 * @param string $id Product ID
	 * @return string HTML of the last seen item for the given product ID
	 */
	protected function _getHtml( $id )
	{
		$context = $this->_getContext();
		$cache = $context->getCache();
		$key = md5( $id . 'product:detail-seen' );

		if( ( $html = $cache->get( $key ) ) === null )
		{
			$expire = null;
			$tags = array();
			$view = $this->getView();
			$config = $context->getConfig();

			$default = array( 'media', 'price', 'text' );
			$domains = $config->get( 'client/html/catalog/domains', $default );

			/** client/html/catalog/detail/seen/domains
			 * A list of domain names whose items should be available in the last seen view template for the product
			 *
			 * The templates rendering product details usually add the images,
			 * prices and texts, etc. associated to the product
			 * item. If you want to display additional or less content, you can
			 * configure your own list of domains (attribute, media, price, product,
			 * text, etc. are domains) whose items are fetched from the storage.
			 * Please keep in mind that the more domains you add to the configuration,
			 * the more time is required for fetching the content!
			 *
			 * @param array List of domain names
			 * @since 2014.07
			 * @category Developer
			 * @see client/html/catalog/domains
			 * @see client/html/catalog/list/domains
			 * @see client/html/catalog/detail/domains
			 */
			$domains = $config->get( 'client/html/catalog/detail/seen/domains', $default );

			$view->seenProductItem = MShop_Factory::createManager( $context, 'product' )->getItem( $id, $domains );

			$this->_addMetaItem( $view->seenProductItem, 'product', $expire, $tags );
			$this->_addMetaList( $view->seenProductItem->getId(), 'product', $expire );

			$output = '';
			foreach( $this->_getSubClients() as $subclient ) {
				$output .= $subclient->setView( $view )->getBody( '', $tags, $expire );
			}
			$view->seenBody = $output;

			/** client/html/catalog/detail/seen/default/template-body
			 * Relative path to the HTML body template of the catalog detail seen client.
			 *
			 * The template file contains the HTML code and processing instructions
			 * to generate the result shown in the body of the frontend. The
			 * configuration string is the path to the template file relative
			 * to the layouts directory (usually in client/html/layouts).
			 *
			 * You can overwrite the template file configuration in extensions and
			 * provide alternative templates. These alternative templates should be
			 * named like the default one but with the string "default" replaced by
			 * an unique name. You may use the name of your project for this. If
			 * you've implemented an alternative client class as well, "default"
			 * should be replaced by the name of the new class.
			 *
			 * @param string Relative path to the template creating code for the HTML page body
			 * @since 2014.03
			 * @category Developer
			 * @see client/html/catalog/detail/seen/default/template-header
			 */
			$tplconf = 'client/html/catalog/detail/seen/default/template-body';
			$default = 'catalog/detail/seen-body-default.html';

			$html = $view->render( $this->_getTemplate( $tplconf, $default ) );

			$cache->set( $key, $html, $tags, $expire );
		}

		return $html;
	}
}