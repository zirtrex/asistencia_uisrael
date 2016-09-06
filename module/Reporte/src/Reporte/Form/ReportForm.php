<?php
namespace Reporte\Form;

use Zend\Form\Form;


class ReportForm extends Form
{	
    public function __construct()
    {
		parent::__construct('ReportForm');
    	
    	$this->setAttribute('method', 'post');  
    	$this->setAttribute('class', 'form-inline');
    	$this->setAttribute('role', 'form');
    	
    	$this->add(array(
    			'name' => 'esComun',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Común:',
    					'empty_option' => '----Seleccione si Es Común----',
    					'value_options' => array(
    							'Si' => 'Si',
    							'No' => 'No'
    					),
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'codAreaConocimiento',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Area del Conocimiento: ',
    					'empty_option' => '----Seleccione Area del Conocimiento----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	 
    	$this->add(array(
    			'name' => 'codCarreraProfesional',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Carrera Profesional: ',
    					'empty_option' => '----Seleccione carrera profesional----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	
    	$this->add(array(
    			'name' => 'codCicloAcademico',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Ciclo académico: ',
    					'empty_option' => '----Seleccione ciclo académico----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	 
    	$this->add(array(
    			'name' => 'codDocente',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Docente: ',
    					'empty_option' => '----Seleccione docente----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	 
    	$this->add(array(
    			'name' => 'codCurso',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Curso: ',
    					'empty_option' => '----Seleccione curso----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	 
    	$this->add(array(
    			'name' => 'codModalidad',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Modalidad: ',
    					'empty_option' => '----Seleccione modalidad----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	 
    	$this->add(array(
    			'name' => 'paralelo',
    			"type"     => 'Zend\Form\Element\Select',
    			"options"  => array(
    					'label' => 'Paralelo: ',
    					'empty_option' => '----Seleccione Paralelo----',
    			),
    			'attributes' => array(
    					'class' => 'form-control',
    			),
    	));
    	
    	$this->add(
    			array(
    					'name' => 'fechaInicioClases',
    					'type' => 'Zend\Form\Element\Text',
    					'attributes' => array(
    							'id' => 'fechaInicioClases',
    							'placeholder' => 'Ingrese fecha de inicio de clases',
    							'class' => 'form-control datepicker',
    					),
    					'options' => array(
    							'label' => 'Fecha de inicio de clases',
    							'label_attributes' => array(
    									'class' => 'col-sm-2 control-label'
    							),
    					),
    			)
    	);
    	
    	$this->add(
    			array(
    					'name' => 'fechaFinClases',
    					'type' => 'Zend\Form\Element\Text',
    					'attributes' => array(
    							'id' => 'fechaFinClases',
    							'placeholder' => 'Ingrese fecha de final de clases',
    							'class' => 'form-control datepicker',
    					),
    					'options' => array(
    							'label' => 'Fecha de final de clases',
    							'label_attributes' => array(
    									'class' => 'col-sm-2 control-label'
    							),
    					),
    			)
    	);
    	
    	$this->add(array(
    			'name' => 'listar',
    			'attributes' => array(
    					'type' => 'submit',
    					'value' => 'Mostrar',
    					'class' => 'btn btn-info',
    			),
    	));
    }
}
