<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\MShop\Index\Manager\Price;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Price\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->clear( array( -1 ) ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/price', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( 'CNC', ['price'] );

		$this->object->delete( $product->getId() );
		$this->object->save( $product );

		$search = $this->object->filter();

		$func = $search->make( 'index.price:value', ['EUR'] );
		$search->setConditions( $search->compare( '==', $func, '18.00' ) );

		$this->assertEquals( 3, count( $this->object->search( $search )->toArray() ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testIterate()
	{
		$filter = $this->object->filter();
		$filter->add( $filter->make( 'index.price:value', ['EUR'] ), '>=', '18.00' );

		$iterator = $this->object->iterator( $filter );
		$products1 = $this->object->iterate( $iterator, [], 10 );
		$products2 = $this->object->iterate( $iterator, [], 10 );

		$this->assertEquals( 10, count( $products1 ) );
		$this->assertEquals( 1, count( $products2 ) );

		foreach( $products1 as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSearchItems()
	{
		$search = $this->object->filter();

		$func = $search->make( 'index.price:value', ['EUR'] );
		$search->setConditions( $search->compare( '>=', $func, '18.00' ) );

		$sortfunc = $search->make( 'sort:index.price:value', ['EUR'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 11, count( $result ) );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}

}
