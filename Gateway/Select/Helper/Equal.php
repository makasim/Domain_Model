<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_Equal extends Domain_Gateway_Select_Helper_GetPrimaryIds
{
	/**
	 * @todo it does not work correctly. must be rewrited
	 */
	public function equal(Domain_Gateway_Select $match_gateway)
	{
		$cur_table_name = $this->_gateway->table()->getName();
		$match_table_name = $match_gateway->table()->getName(); 
		if ($cur_table_name !== $match_table_name) {
			throw new Domain_Model_Exception('Gateway for different tables(Models) can not be compared to equal. Current table is `' . $cur_table_name . '`. Match table is `' . $match_table_name . '`');
		}
		
		$match_select = $this->_prepareSelect($match_gateway->select());
		
		$primary_id = $this->_getTable()->quoteColumnAs();
		$this->_select
			->where("$primary_id NOT IN ({$match_select->assemble()})")
			->distinct();
        
		$this->_gateway->setSelectFields($this->_select, new Zend_Db_Expr('COUNT(*)'));
		
		$diff = (int) $this->_getDbAdapter()->fetchOne($this->_select);
				
		throw new Domain_Gateway_Exception('Not implemented yet');
		
		echo "\n" . $this->_select . "\n";
		var_dump($diff);
		var_dump($diff === 0);
		return $diff === 0;
	}
}