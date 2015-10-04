<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Adds admin log test data.
 */
class MW_Setup_Task_LogAddTestData extends MW_Setup_Task_Base
{
	/**
	 * Returns the list of task names which this task depends on.
	 *
	 * @return string[] List of task names
	 */
	public function getPreDependencies()
	{
		return array( 'MShopSetLocale', 'OrderAddTestData', 'JobAddTestData' );
	}


	/**
	 * Returns the list of task names which depends on this task.
	 *
	 * @return array List of task names
	 */
	public function getPostDependencies()
	{
		return array();
	}


	/**
	 * Executes the task for MySQL databases.
	 */
	protected function mysql()
	{
		$this->process();
	}


	/**
	 * Adds admin log test data.
	 */
	protected function process()
	{
		$iface = 'MShop_Context_Item_Iface';
		if( !( $this->additional instanceof $iface ) ) {
			throw new MW_Setup_Exception( sprintf( 'Additionally provided object is not of type "%1$s"', $iface ) );
		}

		$this->msg( 'Adding admin log test data', 0 );
		$this->additional->setEditor( 'core:unittest' );

		$this->addLogTestData();

		$this->status( 'done' );
	}


	/**
	 * Adds the log test data.
	 *
	 * @throws MW_Setup_Exception If a required ID is not available
	 */
	private function addLogTestData()
	{
		$adminLogManager = MAdmin_Log_Manager_Factory::createManager( $this->additional, 'Standard' );

		$ds = DIRECTORY_SEPARATOR;
		$path = dirname( __FILE__ ) . $ds . 'data' . $ds . 'log.php';

		if( ( $testdata = include( $path ) ) == false ) {
			throw new MShop_Exception( sprintf( 'No file "%1$s" found for log domain', $path ) );
		}

		$log = $adminLogManager->createItem();

		$this->conn->begin();

		foreach( $testdata['log'] as $dataset )
		{
			$log->setId( null );
			$log->setFacility( $dataset['facility'] );
			$log->setPriority( $dataset['priority'] );
			$log->setMessage( $dataset['message'] );
			$log->setRequest( $dataset['request'] );

			$adminLogManager->saveItem( $log, false );
		}

		$this->conn->commit();
	}

}