<?php

/**
 * 
 * @author maksim
 *
 */
class Domain_Gateway_Select implements Domain_Gateway_Interface
{
	protected $_is_init = false;
	
	/**
	 * 
	 * @var Domain_Table_Abstract
	 */
	protected $_table = null;
	
	/**
	 * @var Zend_Db_Table_Select
	 */
	protected $_select = null;
	
	protected $_helper_loader = null;
	
	/**
	 * @var array|false
	 */
	protected $_primary_ids = false;
	
	/**
	 * @var int|false
	 */
	protected $_count = false;
	
	/**
	 * 
	 * Enter description here...
	 * @param array $options
	 * @return void
	 */
	public function __construct(array $options = array())
	{
		if (is_array($options) && 
				array_key_exists('tableClass', $options) && 
					class_exists($options['tableClass'], true))	{
			$this->_table = new $options['tableClass'];
		} elseif (is_string($options) && class_exists($options, true)) {
			$this->_table = new $options;
		} else {
			throw new Domain_Model_Exception('Table class was not given. Valid parameters array with key `tableClass` or string(table class name)');
		}
	}
	
	public function get(array $fields = array())
	{
		$select = $this->setSelectFields($this->select(), $fields);
		return $this->_getAdapter()->fetchAll($select);
	}

	/**
	 *
	 * @param $data
	 * @return int          The number of rows deleted.
	 */
	public function delete()
	{
		if (count($this->_getSelect()->getPart(Zend_Db_Select::SQL_FROM)) == 0) {
			$where = implode(' ', $this->_getSelect()->getPart(Zend_Db_Select::SQL_WHERE));
			return $this->table()->delete($where);	
		}
		
		$from = strstr($this->_getSelect()->assemble(), 'FROM');
		$query = 'DELETE ' . $this->table()->getName() . ' ' . $from;

		return $this->_getAdapter()->query($query)->rowCount();
	}

	/**
	 *
	 * @param $data
	 * @return int The number of rows updated.
	 */
	public function update(array $data)
	{
		if (count($this->_getSelect()->getPart(Zend_Db_Select::SQL_FROM)) == 0) {
			$where = implode(' ', $this->_getSelect()->getPart(Zend_Db_Select::SQL_WHERE));
			return $this->table()->update($data, $where);
		}

		$set = array();
		$data = array_intersect_key($data, array_flip($this->table()->getColumns()));
		foreach($data as $name => $value) {
			$set[] = ' ' . $this->table()->quoteColumnAs($name) . ' = "' . $value . '" ';
		}
		$set = ' SET ' . implode(' , ', $set);
		$where = ' WHERE ' . implode(' ', $this->_getSelect()->getPart(Zend_Db_Select::SQL_WHERE));
		
		foreach ($this->_getSelect()->getPart(Zend_Db_Select::SQL_FROM) as $table => $options) {
			if (!isset($from)) {
				$from = " $table ";
				continue;
			}
			
			$from .= " {$options['joinType']} {$table} ON {$options['joinCondition']} ";
		}
		
		$query = "UPDATE $from $set $where";
	
		return $this->_getAdapter()->query($query)->rowCount();
	}

	public function count()
	{
		$select = $this->select()->group($this->table()->quoteColumnAs());
		return Zend_Paginator::factory($select)->getTotalItemCount();
	}
	
	public function create(array $data)
	{
		$id = $this->table()->createRow($data)->save();
		$this->initById($id);
		return $id;
	}
	
	public function initById($id)
	{
		$primary = $this->table()->quoteColumnAs();
		$this->setCriteria($this->table()->select()->where("$primary IN (?)", $id));
		return $this;
	}
	
	/**
	 * 
	 * Enter description here...
	 * @return unknown_type
	 */
	public function initByAll()
	{
		$this->setCriteria($this->table()->select());
		return $this;
	}
	
	public function initByEmpty()
	{
		$this->initById(-1);
		return $this;
	}
	
	/**
	 *
	 * @return Zend_Db_Table_Select
	 */
	public function select()
	{
		return clone $this->_getSelect();
	}

	/**
	 *
	 * @return Domain_Table_Abstract
	 */
	public function table()
	{
		return $this->_table;
	}
			
	public function __call($name, $args = array())
	{
		if (is_null($this->_helper_loader)) {
			$this->_helper_loader = new Domain_Gateway_Helper_Loader(
				$this, array('Domain_Gateway_Select_Helper' => 'Domain/Gateway/Select/Helper/'));
		}
		
		return call_user_func_array(array($this->_helper_loader, $name), $args);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see Lib/Domain/Gateway/Domain_Gateway_Interface#setCriteria($criteia)
	 */
	public function setCriteria($criteia)
	{
		$this->_setSelect($criteia);
		return $this;
	}
	
	public function isInit()
	{
		return $this->_is_init;
	}
	
	/**
	 * @return Zend_Db_Table_Select
	 */
	protected function _setSelect(Zend_Db_Table_Select $select)
	{
		if ($this->_select instanceof Zend_Db_Table_Select) {
			throw new Domain_Model_Exception('Gateway is already intialized and cannot be reinit');
		}
		if ($select->getTable()->getName() != $this->table()->getName()) {
			throw new Domain_Model_Exception('The select object(given) must be referenced with table ' . $this->table()->getName() . '. Given select refered to ' . $select->getTable()->getName());
		}
		
		$this->_is_init = true;
		$this->_select = $select;
	}

	protected function _getSelect()
	{
		if (!$this->isInit()) {
			throw new Domain_Model_Exception('Gateway must be initialized by `Zend_Db_Table_Select` object befor any other use');
		}

		return $this->_select;
	}
	
	/**
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected function _getAdapter()
	{
		return $this->_getSelect()->getTable()->getAdapter();
	}
}