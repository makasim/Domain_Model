<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_GetPrimaryId extends Domain_Gateway_Select_Helper_GetPrimaryIds
{
	/**
	 * 
	 * Enter description here...
	 * @return int
	 */
	public function getPrimaryId()
	{
		$this->_select->limit(1);
		return $this->_getDbAdapter()->fetchOne($this->_select);
	}
}