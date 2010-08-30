<?php
class Domain_Model_Referance_Item extends ArrayObject
{
	public function __construct($name, $options)
	{
		$availiable_options = array(
			'parentModelClass' => null, 'dependentModelClass' => null, 'tableReferanceRule' => null);
		
		$options = array_intersect_key($options, $availiable_options);
		
		if (is_null($options['tableReferanceRule'])) {
			$options['tableReferanceRule'] = $name;
		}
		
		if (empty($options['dependentModelClass'])) {
			throw new Domain_Model_Exception('`dependentModelClass` is requiered');
		}
		
		if (empty($options['parentModelClass'])) {
			throw new Domain_Model_Exception('`parentModelClass` is requiered');
		}
		
		$options['referanceName'] = $name;
		
		parent::__construct($options);
	}
	
	public function isParent($model_class)
	{
		return $model_class === $this->getParentModelClass();
	}
	
	public function isDependent($model_class)
	{
		return $model_class === $this->getDependentModelClass();
	}
	
	public function getTableRule()
	{
		return $this->offsetGet('tableReferanceRule');
	}
	
	public function getName()
	{
		return $this->offsetGet('referanceName');
	}
	
	public function getParentModelClass()
	{
		return $this->offsetGet('parentModelClass');
	}
	
	public function getDependentModelClass()
	{
		return $this->offsetGet('dependentModelClass');
	}
}