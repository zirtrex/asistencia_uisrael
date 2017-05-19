<?php

namespace Admin\Entity;

class Config {	

	protected $idConfig;
	
	protected $name;
	
	protected $value;

	public function getIdConfig()
    {
        return $this->idConfig;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setIdConfig($idConfig)
    {
        $this->idConfig = $idConfig;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function exchangeArray(Array $data)
	{
		$this->idConfig = (isset($data['idConfig'])) ? $data['idConfig'] : null;
	
		$this->name = (isset($data['name'])) ? $data['name'] : null;
	
		$this->value = (isset($data['value'])) ? $data['value'] : null;		 
	}
	
	public function getArrayCopy()
	{
		return get_object_vars($this);
	}	
		
}
