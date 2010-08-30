<?php
abstract class Domain_Gateway_Select_Helper_GetReferancedAbstract extends Domain_Gateway_Select_Helper_Abstract
{
	protected function _prepareTable($table)
	{
		$preparedTable = null;
		
		if ($table instanceof Domain_Gateway_Select) {
			$preparedTable = $table->table();
		} else if ($table instanceof Domain_Table_Abstract) {
			$preparedTable = $table;
		} else if ($table instanceof Zend_Db_Table_Select) {
			$preparedTable = $table->getTable();
		} else {
			throw new Domain_Gateway_Exception('Invalid paramter, cannot extract the table from it.');
		}
		
		return $preparedTable;
	}
	
	protected function _prepareSelect(Domain_Gateway_Select $gateway)
	{
		$preparedSelect = null;
		if ($gateway instanceof Domain_Gateway_Select) {
			$preparedSelect = $gateway->simplifyCriteria();
		}
		
		return $preparedSelect;
	}
}