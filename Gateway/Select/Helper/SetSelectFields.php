<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select_Helper_SetSelectFields extends Domain_Gateway_Select_Helper_Abstract
{
	public function setSelectFields(
		Zend_Db_Table_Select $select, $fields = Zend_Db_Table_Select::SQL_WILDCARD)
	{
		$table = $select->getTable();
		
		$fields = is_array($fields) ? $fields : array($fields);
		foreach ($fields as $field) {
        	$fields_quoted[] = ($field instanceof Zend_Db_Expr) ? $field : $table->quoteColumnAs($field);
        }

		if (!count($select->getPart(Zend_Db_Table_Select::COLUMNS))) {
			$select->from($table->getName(), $fields_quoted);
		} else {
			$select->reset(Zend_Db_Select::COLUMNS);	
			$select->columns($fields_quoted);
		}
		
		return $select;
	}
}