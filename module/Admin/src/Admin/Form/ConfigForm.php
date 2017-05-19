<?php
namespace Admin\Form;
 
use Zend\Form\Form;
use Zend\Stdlib\Hydrator\ClassMethods as ClassMethodsHydrator;
use Admin\Entity\Config;


class ConfigForm extends Form
{
    public function __construct()
    {
        parent::__construct('ConfigForm');
        
        $this->setHydrator(new ClassMethodsHydrator(false))
        	->setObject(new Config());
 
        $this->setAttributes(array('method' => 'post'));
        
        $this->add(
        		array(
        				'name' => 'idConfig',
        				'type' => 'hidden',
        				'attributes' => array(
        						'id' => 'codAula'
        				),
        		)
        );

        $this->add(
                array(
                        'name' => 'name',
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => array(
                                'id' => 'name',
                                'placeholder' => 'Ingrese nombre de la Configuración',
                                'required' => 'required',
                                'class' => 'form-control',
                        ),
                        'options' => array(
                                'label' => 'Nombre',
                                'label_attributes' => array(
                                        'class' => 'col-sm-2 control-label'
                                ),
                        ),
                )
        );

        $this->add(
                array(
                        'name' => 'value',
                        'type' => 'Zend\Form\Element\Text',
                        'attributes' => array(
                                'id' => 'value',
                                'placeholder' => 'Ingrese valor de la Configuración',
                                'required' => 'required',
                                'class' => 'form-control',
                        ),
                        'options' => array(
                                'label' => 'Valor',
                                'label_attributes' => array(
                                        'class' => 'col-sm-2 control-label'
                                ),
                        ),
                )
        );        
 
        $this->add(
        		array(
        				'name' => 'guardar',
        				'type' => 'Zend\Form\Element\Submit',
        				'attributes' => array(
        						'value' => 'Guardar',
        						'class' => 'btn btn-success',
        				),
        		)
        );
    }
}