<?php
namespace Admin\Form\Filter;

use Zend\InputFilter\InputFilter;

class AulaFilter extends InputFilter
{    
    public function __construct()
    {
        
    	$this->add(
    			array(
    					'name' => 'codAula',
    					'required' => false,
    			)
    	);
    	  	
    	$this->add(
                array(
                        'name' => 'numero',
                        'required' => true,
                        'filters' => array(
                                array('name' => 'Int')
                        ),
                        'validators' => array(
                             array(
                                  'name' => 'Between',
                                  'options' => array(
                                      'min' => 1,
                                      'max' => 600,
                                  ),
                              ),
                        ),
                )
        );

        $this->add(
                array(
                        'name' => 'piso',
                        'required' => true,
                        'filters' => array(
                                array('name' => 'Int')
                        ),
                        'validators' => array(
                             array(
                                  'name' => 'Between',
                                  'options' => array(
                                      'min' => 1,
                                      'max' => 4,
                                  ),
                              ),
                        ),
                )
        );

        $this->add(
                array(
                        'name' => 'capacidad',
                        'required' => true,
                        'filters' => array(
                                array('name' => 'Int')
                        ),
                        'validators' => array(
                             array(
                                  'name' => 'Between',
                                  'options' => array(
                                      'min' => 1,
                                      'max' => 100,
                                  ),
                              ),
                        ),
                )
        );

    }
}