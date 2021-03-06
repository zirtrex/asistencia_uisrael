<?php
namespace Admin\Form\Filter;

use Zend\InputFilter\InputFilter;

class SilaboFilter extends InputFilter
{    
    public function __construct()
    {
    	$this->add(
    			array(
    					'name' => 'codSemana',
    					'required' => true,
    			)
    	);
    	
    	$this->add(
    			array(
    					'name' => 'tematica',
    					'required' => true,
    					'filters' => array(
    							array('name' => 'StripTags'),
    							array('name' => 'StringTrim'),
    					),
    					'validators' => array(
    							array(
    									'name' => 'StringLength',
    									'options' => array(
    											'encoding' => 'UTF-8',
    											'min' => 4,
    											'max' => 200,
    									),
    							),
    					),
    			)
    	);   	

    }
    
}