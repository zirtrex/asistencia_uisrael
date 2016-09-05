<?php
namespace Reporte\Form\Filter;

use Zend\InputFilter\InputFilter;

class ReportFormFilter extends InputFilter
{    
    public function __construct()
    {
    	/*$this->add(
    			array(
    					'name' => 'fechaInicioClases',
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
    											'min' => 9,
    											'max' => 10,
    									),
    							),
    					),
    			)
    	);*/
    	
		
		$this->add(
				array(
						'name' => 'esComun',
						'required' => true,
						/*'validators' => array(
								array(
										'name' => 'not_empty'
								),
						),*/
				)
		);
		
		$this->add(
				array(
						'name' => 'codAreaConocimiento',
						'required' => false
				)
		);
		
		$this->add(
				array(
						'name' => 'codCarreraProfesional',
						'required' => false
				)
		);
		
		$this->add(
				array(
						'name' => 'codCicloAcademico',
						'required' => true
				)
		);
		
		$this->add(
				array(
						'name' => 'codCurso',
						'required' => true
				)
		);
		
		$this->add(
				array(
						'name' => 'codModalidad',
						'required' => true
				)
		);
		
		$this->add(
				array(
						'name' => 'paralelo',
						'required' => true
				)
		);
		
		$this->add(
				array(
						'name' => 'codDocente',
						'required' => true
				)
		);
        
    }
    
}