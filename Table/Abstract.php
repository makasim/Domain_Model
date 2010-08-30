<?php
abstract class Domain_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->info(Zend_Db_Table::NAME);
	}
	
	/**
	 * 
	 * @return mixed
	 */
	public function getPrimary()
	{
		return $this->info(Zend_Db_Table::PRIMARY);
	}
	
	/**
	 * 
	 * @return array (index => col_name)
	 */
	public function getColumns()
	{
		return $this->info(Zend_Db_Table::COLS);
	}
	
	/**
	 * 
	 * @param $parent_table
	 * @param $rule
	 * @param $select_this
	 * @param $select_parent
	 * @return Zend_Db_Table_Select
	 */
	public function getSelectToParent($parent_table, $rule, $select_this = null, $select_parent = null)
	{
        $parent_table = $this->_prepareTable($parent_table);
        $select = null === $select_parent ?
        	$parent_table->select() : $select_parent->setTable($parent_table);
        
        $select = $this->_prepareReference($this, $parent_table, $rule, $select);
        if (!is_null($select_this) && $select_this instanceof Zend_Db_Table_Select) {
      		$this->_mergeWheres($select, $select_this);	
      	}
        
      	return $select;
	}
	
	public function getSelectToDependent($dependentTable, $rule, $select_this = null, $select_dependent = null)
	{
		$dependentTable = $this->_prepareTable($dependentTable);
       
        $select = $select_dependent === null ? $dependentTable->select() : 
        	$select_dependent->setTable($dependentTable);
        $select = $this->_prepareReference($dependentTable, $this, $rule, $select); 
		if (!is_null($select_this) && $select_this instanceof Zend_Db_Table_Select) {
      		$this->_mergeWheres($select, $select_this);	
      	}
		
      	return $select;
	}
	
	public function getSelectManyToMany($matchTable, $intersectionTable, $callerRefRule = null, 
		$matchRefRule = null, $select_this = null, $select_match = null)
	{
		$intersectionTable = $this->_prepareTable($intersectionTable);
		$matchTable = $this->_prepareTable($matchTable);
		
        $select = $select_match === null ? $matchTable->select() : $select_match->setTable($matchTable);
        $select = $this->_prepareReference($intersectionTable, $matchTable, $matchRefRule, $select);
		$select = $this->_prepareReference($intersectionTable, $this, $callerRefRule, $select);
		if (!is_null($select_this) && $select_this instanceof Zend_Db_Table_Select) {
      		$this->_mergeWheres($select, $select_this);	
      	}
		      	
      	return $select;
	}
	
	/**
	 * 
	 * @param $column Columns that depends to the table. If null the primary key will be returned
	 * @param $alias
	 * @param $auto
	 * @return string quoted column
	 */
	public function quoteColumnAs($column = Zend_Db_Table::PRIMARY, $alias = null, $auto=false)
	{
		$primary_id_columns = $this->getPrimary();
		Zend_Db_Table::PRIMARY === $column && $column = array_shift($primary_id_columns);
		if (!in_array($column, $this->getColumns())) {
			throw new Domain_Table_Exception('The table does not have given column:' . $column);
		}
		
		return new Zend_Db_Expr(
			$this->getAdapter()->quoteColumnAs(array($this->getName(), $column), $alias, $auto));
	}
	
	protected function _prepareTable($name)
	{
        if (!$name instanceof Zend_Db_Table_Abstract) {
        	$type = gettype($name);
            if ($type == 'object') {
                $type = get_class($name);
            }
            require_once 'Zend/Db/Table/Row/Exception.php';
            throw new Zend_Db_Table_Row_Exception("Parent table must be a Zend_Db_Table_Abstract, but it is $type");
        }
        
        return $name;
	}
	
	protected function _prepareReference(Zend_Db_Table_Abstract $dependentTable, Zend_Db_Table_Abstract $parentTable, 
		$ruleKey, Zend_Db_Table_Select $select)
    {
    	$map = $dependentTable->getReference(get_class($parentTable), $ruleKey);
    	
        if (!isset($map[Zend_Db_Table_Abstract::REF_COLUMNS])) {
            $parentInfo = $parentTable->info();
            $map[Zend_Db_Table_Abstract::REF_COLUMNS] = array_values((array) $parentInfo['primary']);
        }

        $map[Zend_Db_Table_Abstract::COLUMNS] = (array) $map[Zend_Db_Table_Abstract::COLUMNS];
        $map[Zend_Db_Table_Abstract::REF_COLUMNS] = (array) $map[Zend_Db_Table_Abstract::REF_COLUMNS];

        $select->setIntegrityCheck(false);
        $dependentTableName = $dependentTable->info(Zend_Db_Table::NAME);
        $parentTableName = $parentTable->info(Zend_Db_Table::NAME);
       	
        $joinCond = array();
        foreach ($map[Zend_Db_Table_Abstract::COLUMNS] as $key => $column) {
        	$ref_column = $map[Zend_Db_Table_Abstract::REF_COLUMNS][$key];
      
        	$dependent_column = "{$dependentTableName}.{$column}";
            $dependent_column = $this->getAdapter()->quoteIdentifier($dependent_column, true);
            $parent_column = "{$parentTableName}.{$ref_column}";
            $parent_column = $this->getAdapter()->quoteIdentifier($parent_column, true);
            
            if ($parentTableName === $select->getTable()->info(Zend_Db_Table::NAME)) {
            	$joinCond[] = "$parent_column = $dependent_column";	
            } else {
            	$joinCond[] = "$dependent_column = $parent_column";
            }
        }
        
        $joinCond = implode(' AND ', $joinCond);
        //without it anything cannot work.
        $select->assemble();
        if ($parentTableName === $select->getTable()->info(Zend_Db_Table::NAME)) {
        	$select->joinInner($dependentTableName, $joinCond, null);
        } else {
        	$select->joinInner($parentTableName, $joinCond, null);
        }
       
        return $select;
    }
    
    protected function _mergeWheres(Zend_Db_Table_Select $main_select, Zend_Db_Table_Select $select)
    {
    	//merge from section
    	/*$main_table_from_section = $select->getPart(Zend_Db_Select::SQL_FROM);
    	foreach ($select->getPart(Zend_Db_Select::SQL_FROM) as $table => $condition) {
    		//if(!isset($main_table_from_section[$table])) {
    			$main_select->join($table, $condition['joinCondition']); 
    		//}
    	}*/
    	//TODO: maybe it is needed to implement group by merge.
    	
   		$where = implode(' ', $select->getPart(Zend_Db_Select::SQL_WHERE));
    	if ('(' === substr($where, 0, 1)) {
    		$where = substr_replace($where, '', 0, 1);
    	}
    	if (')' === substr($where, -1, 1)) {
    		$where = substr_replace($where, '', -1, 1);
    	}
    	
    	!empty($where) && $main_select->where($where); 
    }
}