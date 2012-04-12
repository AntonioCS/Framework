<?php

class acs_wrapper {

    public $_output = array();
    public $_output_submit = array();

    public $_outter_div_class = 'element_div_wrapper';
    public $_label_class = 'label_div_wrapper';
    public $_element_id = '%containerName%_element_%elementName%';

    public $_element_div_class = 'container_item';

    public $_wrapper_suffix = '_wrapper';
    public $_wrapper_prefix = '';
    
    public $_container_div_id = '';
    public $_container_div_class = '';

    public $_containerName = null;

}

class acs_wrapper_div extends acs_wrapper {

    public function __construct($container_name) {
        //$this->_fname = str_replace(' ','_',$fname);
        $this->_containerName = str_replace(' ','_',$container_name);
    }
	
	/**
	 * Wrapper for wrap with no wrap (haha)
	 * 
	 * @param type $element 
	 */
	public function noWrap($element) {
		$this->wrap($element, true);
	}

    /**
    * Wrap the element in the div
    *
    * @param acs_element $element
    * @param bool $nowrap
    */
    public function wrap($element, $nowrap = false) {

        //This is for cases where a hidden element needs to be before a normal element.
        //With this I can just specify that I do not want it to be wrapped
        if (!$nowrap && !is_a($element,'acs_div')) {
            $id_element = $element->getAttribute('name');
            if (!$id_element)
                $id_element = $element->getAttribute ('id');
            
            $element_type = ($element->getAttribute('type') ? 'acs_form_' . $element->getAttribute('type') : null);

            $wrapper_id = ($id_element ? $id_element . $this->_wrapper_suffix : null);

            $output = null;

            $outter_div = new acs_div();
            $outter_div->setId($wrapper_id);
            $outter_div->setClass($this->_outter_div_class);
           
            if ($element->getLabel()) {                
                $outter_div->add(new acs_form_label($id_element, $element->getLabel()));
            }
           
            $element_div = new acs_div();
            //$element_div->setId( 'acs_form_' . $this->_fname . '_element_' .$id_element . '_wrapper')
			
            $element_div->setId($id_element ? $id_element . '_div' : '')
                            ->setClass($this->_element_div_class);

            $output = $outter_div->addThis($element_div->addThis($element))->html();
        }
        else
            $output = (is_object($element) ? $element->html() : $element);

        $this->_output[] = $output;
    }

    /**
    * Process 'submit' or 'reset' input elements
    *
    * @param mixed $element
    */
    public function wrapSubmit($element) {
        $this->wrap($element);
        $this->_output_submit[] = array_pop($this->_output); //retrieve the 'submit' or 'reset' input elements from the output list
    }

    /**
    * Create the final output
    *
    */
    public function output() {
        $container_div = new acs_div();
        $container_div->setId($this->_containerName ? 'container_' . $this->_containerName : '')
                    ->setClass('acs_form_element')
                    ->add(implode("\n",$this->_output));

        if (!empty($this->_output_submit)) {         
            
            $submitWrapper = new acs_div();
            $submitWrapper->setId('submit_' .$this->_containerName)
                            ->setClass('acs_form_submit_elements');
                            
            
            $container_div->add(
                            $submitWrapper->addThis(
                                                    implode("\n",$this->_output_submit)
                                                 )
                               );
        }

        return $container_div->html();
    }
}