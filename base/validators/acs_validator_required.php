<?php
  
  
class acs_validator_required extends acs_validator_base {

    protected $_error_msg = 'This value is required';
    
    public function validate($value = null) {
        if ($value === null) 
            return false;
            
        return true;    
    }
    
    public function modifier(acs_element $element) {        
        
        $element->appendAttribute('class', 'required');        
    }
}
