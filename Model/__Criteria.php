<?php
class Domain_Model_Criteria implements Domain_Model_Criteria_Interface
{
	protected $_select = null;
	
	protected $_primary_ids = false;
	
	protected $_count = false;

	public function __construct($options)
	{
		if (is_array($options) && 
				array_key_exists('tableClass', $options) && 
					class_exists($options['tableClass'], true))	{
			$this->_initSelect(new $options['tableClass']);
		} elseif (is_string($options) && class_exists($options, true)) {
			$this->_initSelect(new $options);
		} else {
			throw new Domain_Model_Exception('Table class was not given. Valid parameters array with key `tableClass` or string(table class name)');
		}
	}
	
	protected function _initSelect($table)
	{
		if (!$table instanceof Domain_Table_Abstract) {
			throw new Domain_Model_Exception('The table class must be instance of `Domain_Table_Abstract`');
		}
		
		$this->setSelect($table->select());
	}

	public function getSelectForReferanceModel()
	{
		if ($this->_isSimple()) {
			return $this->select();
		}

		$primary = $this->table()->quoteColumnAs();
		return $this->table()->select()->where("$primary  IN (?)", $this->getPrimaryIds());
	}

	/**
	 *
	 * @param $parent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function getCriteriaToParent(Domain_Model_Criteria $parent, $rule)
	{
		$select = $this->table()->getSelectToParent(
		$parent->table(),
		$rule,
		$this->getSelectForReferanceModel(),
		$parent->getSelectForReferanceModel());

		$class = get_called_class();
		return new $class($select, $useable = false);
	}

	/**
	 *
	 * @param $dependent
	 * @param $rule
	 * @return Domain_Model_Criteria
	 */
	public function getCriteriaToDependent(Domain_Model_Criteria $dependent, $rule)
	{
		$select = $this->table()->getSelectToDependent(
		$dependent->table(),
		$rule,
		$this->getSelectForReferanceModel(),
		$dependent->getSelectForReferanceModel());

		$class = get_called_class();
		return new $class($select, $useable = false);
	}

	/**
	 *
	 * @param $matchCriteria
	 * @param $intersectionTable
	 * @param $callerRefRule
	 * @param $matchRefRule
	 * @return Domain_Model_Criteria
	 */
	public function getCriteriaManyToMany(Domain_Model_Criteria $matchCriteria, $intersectionTable,
	$callerRefRule, $matchRefRule)
	{
		$select = $this->table()->getSelectManyToMany(
			$matchCriteria->table(),
			$intersectionTable,
			$callerRefRule,
			$matchRefRule,
			$this->getSelectForReferanceModel(),
			$matchCriteria->getSelectForReferanceModel());
		
		$class = get_called_class();
		return new $class($select, $useable = false);
	}

	/**
	 *
	 * @param $data
	 * @return int          The number of rows deleted.
	 */
	public function delete()
	{
		if ($this->_isSimple()) {
			$where = implode(' ', $this->_getSelect()->getPart(Zend_Db_Select::SQL_WHERE));
			return $this->table()->delete($where);	
		}
		
		$from = strstr($this->_getSelect()->assemble(), 'FROM');
		$query = 'DELETE ' . $this->table()->getName() . ' ' . $from;

		return $this->_getAdapter()->query($query)->rowCount();
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
	 * @param $data
	 * @return int The number of rows updated.
	 */
	public function update(array $data)
	{
		if ($this->_isSimple()) {
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

	/**
	 *
	 * @return Domain_Table_Abstract
	 */
	public function table()
	{
		return $this->_getSelect()->getTable();
	}
			
	public function __call($name, $args = array())
	{
		return Domain_Model_Criteria_Helper_Abstract::factory($name, $this)->call($args);
	}

	/**
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	protected function _getAdapter()
	{
		return $this->_getSelect()->getTable()->getAdapter();
	}

	/**
	 *
	 * If model was created from simple query that this parameter true
	 * If model was created throught many join`s that this parameter false
	 * 
	 * If it is set to false. This query will not used to build next relation queries.
	 * The primary key will be extracted from the query and used for next uses.
	 *
	 * @return bool
	 */
	protected function _isSimple()
	{
		return count($this->_getSelect()->getPart(Zend_Db_Select::SQL_FROM)) == 0;
	}
	
	/**
	 * @return Zend_Db_Table_Select
	 */
	public function setSelect(Zend_Db_Table_Select $select)
	{
		$this->_select = $select;
	}

	protected function _getSelect()
	{
		if (!$this->_select instanceof Zend_Db_Table_Select) {
			throw new Domain_Model_Exception('Select must be defined befor any other use');
		}

		return $this->_select;
	}

	//protected function _cle

	/**
	 *
	 * @return Domain_Model_Criteria
	 */
	public static function buildEmpty(Domain_Table_Abstract $table)
	{
		$class = get_called_class();
		return new $class($table->select());
	}

	/**
	 *
	 * @return Domain_Model_Criteria
	 */
	public static function buildById(Domain_Table_Abstract $table, $id)
	{
		$primary = $table->quoteColumnAs();
		$class = get_called_class();
		return new $class($table->select()->where("$primary IN (?)", $id));
	}
	
	/**
	 *
	 * @return Domain_Model_Criteria
	 */
	public static function buildFromSelect(Zend_Db_Table_Select $select)
	{
		$class = get_called_class();
		return new $class($select);
	}

	public static function create(Domain_Table_Abstract $table, $data)
	{
		$id = $table->createRow($data)->save();
		$primary = $table->quoteColumnAs();
		$class = get_called_class();
		return new $class($table->select()->where("$primary IN (?)", $id));
	}
}