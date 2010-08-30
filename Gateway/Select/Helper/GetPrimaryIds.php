<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_GetPrimaryIds extends Domain_Gateway_Select_Helper_Abstract
{
	/**
	 * @var Zend_Db_Table_Select
	 */
	protected $_select = null;
	
	/**
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Helper/Domain_Gateway_Helper_Abstract#_init()
	 */
	protected function _init()
	{
		$this->_select = $this->_gateway->select();
		$this->_prepareSelect($this->_select);
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return array
	 */
	public function getPrimaryIds()
	{
		return $this->_getDbAdapter()->fetchCol($this->_select);
	}
	
	/**
	 *
	 */
	protected function _prepareSelect(Zend_Db_Table_Select $select)
	{
		$table = $this->_getTable();
		$db =  $this->_getDbAdapter();
		
		$select->distinct();
		$select->group($table->quoteColumnAs());
        $select = $this->_gateway->setSelectFields($select, $table->quoteColumnAs());
		
		return $select;
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected function _getDbAdapter()
	{
		return $this->_getTable()->getAdapter();
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return Domain_Table_Abstract
	 */
	protected function _getTable()
	{
		return $this->_gateway->table();
	}
}