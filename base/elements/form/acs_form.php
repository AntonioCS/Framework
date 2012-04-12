<?php


/*
* Class Forms
* Main class
*     This class will be used to hold all the controls (which will also be objects)
*
* For info - http://www.w3schools.com/TAGS/tag_form.asp
*
*/

/**
*
*
*  TODO  Try to implement something similiar to sugarcrm's way
*  Create a layout and have settings for colums etc. Add some parameteres automatically
*  Set option to create table layout set to true by default with 1 column
*
* !!!READ!!!
* http://line25.com/articles/10-usability-crimes-you-really-shouldnt-commit
*
* More reading material
* http://www.admixweb.com/2009/12/22/15-best-practices-tips-designing-web-form/
*
*/

class acs_form extends acs_element_container {

    //private $_wrapper = 'div';

    //private $_class_form = 'acs_form';

    /**
    * Holds the forms name
    *
    * @var string
    */
    private $_name = 'acsf_';
    /**
    * Form method
    *
    * @var string
    */
    private $_method = 'post'; //Defaults do post
    /**
    * Encription type
    *
    * @var string
    */
    private $_enctype = 'application/x-www-form-urlencoded'; //Default

    private $_accept_charset = 'UTF-8';

    /**
    * For the MAX_FILE_SIZE field
    * If this is null when a file type is added the  upload_max_filesize directive of the php.ini will be used
    *
    * @var mixed
    */
    private $_max_file_size = null;


    /**
    * Create a layout (using the table's class)
    *
    * @var int
    */
    //public $layout = true;


    /**
    * Don't populate passwords
    *
    * @var bool
    */
    private $_nopopulatepass = true;

    //Default values for the layout table
    /*
    public $layout_width = array(
                                'total' => '60%',
                                'label' => '20%',
                                'field' => '40%'
                            );
    public $layout_maxCols = '2';

    public $useLayoyt = null;
    */

    /**
    * Construct of the class
    *
    * @param mixed $fname
    * @return acs_forms
    */

    public function __construct($fname = null) {
        parent::__construct();

        $default_attributes = array(
                                'method' => $this->_method,
                                'enctype' => $this->_enctype,
                                'accept-charset' => $this->_accept_charset,
                                'id' => $fname        			
                              );
        $this->addHidden('fname', $fname);

        $this->mergeAttributes($default_attributes);
		
        $this->tpl_path = 'html/forms/form';
		
		$this->setProcessWrap();
    }

    /**
    * Populate existing form elements with the data given
    *
    * @param array $data
    */
    public function populate(array $data) {
        if (!empty($this->_elements)) {
            foreach ($this->_elements as $k => $e) {
                if (!is_object($e))
                    continue;
                else {
                    if (isset($data[$k])) {
                        $value = $data[$k];

                        switch (true) {
                            case ($e instanceof acs_form_input):
                                switch ($e->type) {
                                    case 'file':
                                        //do nothing
                                    break;
                                    case 'checkbox':
                                        //TODO: Add code to check if a value and if the checkbox is just to be set the value or also to be checked
                                        /*
                                        if (is_array($value)) {
                                            if ($value['checked'] != false)
                                                $e->checked();

                                        }
                                        */
                                    break;

                                    //TODO: The radios are in a group. I must check the value to know witch one is selected
                                    case 'radio':
                                    break;
                                    default:
                                        $e->setValue($value);
                                }
                            break;

                            case ($e instanceof acs_form_textarea):
                                $e->text($value);
                            break;
                            case ($e instanceof acs_form_select):
                                $e->select($value);
                            break;
                        }
                    }
                }
            }
        }
    }

    //Remenber to change the enctype (http://www.w3schools.com/TAGS/att_form_enctype.asp) of the form to multipart/form-data
    //The input class must automatically add a hidden field with a default size in place
    //http://www.scanit.be/uploads/php-file-upload.pdf <-- Securty stuff - MUST READ WHEN IMPLEMENTING THE MODEL TO HANDLE FILE UPLOADS
    public function addFile($element_name = null,$element_label = null, $maxfilesize = null) {
        $this->setAttribute('enctype','multipart/form-data');

        if (!$maxfilesize)
            $this->_max_file_size = $maxfilesize;        
        elseif (!$this->_max_file_size)
            $this->_max_file_size = helper_file::convertBytes(ini_get('upload_max_filesize'));

        $this->addHidden('MAX_FILE_SIZE')->setValue($this->_max_file_size);


        return $this->addElement('input',$element_name,$element_label)->setType('file');
    }

    /**
     * Set the max size file
     * 
     * @param int $value
     * @return acs_form 
     */
    public function setMaxFileSize($value) {
        $this->_max_file_size = (int)$value;
        return $this;
    }
    
    
    /**
    * Method that generates all the code
    *
    * TODO: Add tabindex to the elements - http://www.htmlcodetutorial.com/forms/_INPUT_TABINDEX.html
    *
    */
    public function beforeHtml() {
        $form_name = $this->getAttribute('id');

        if ($this->getAttribute('method') == 'post') {

            $uni = acs_session::getInstance()->createToken($form_name);

            $this->addHidden('token')->setValue($uni)->setId('token_' . $form_name);
            $this->addHidden('token_name')->setId('token_name' . $form_name)->setValue($form_name);
        }

        $classe = 'acs_form';

        if ($this->getAttribute('class'))
            $classe = $this->getAttribute('class') . ' ' . $classe;

        $this->setAttribute('class',$classe);                        
        
		return parent::beforeHtml(array(), 'form_' . $form_name);		
        //return $this->processElementsWrap();
                                  
        
        //Old process code!!        

        $hidden = array();

        $wrap_class = 'acs_form_output_handler_' . $this->_wrapper;
        $wrapper = new  $wrap_class($form_name);

        foreach ($this->_elements as $element) {
            if (!is_object($element))
                $wrapper->wrap($element,true);
            else {
                switch ($element->type) {
                    case 'hidden':
                        if ($element->getAttribute('name') == 'MAX_FILE_SIZE')
                            $wrapper->wrap($element,true);
                        else
                            $hidden[] = $element->html();
                    break;
                    case 'submit':
                    case 'reset':
                       $wrapper->wrapSubmit($element);
                    break;
                    default:
                        $wrapper->wrap($element);
                }
            }
        }

        return array(
            'elements' => $wrapper->output($form_name) .
                        implode("\n",$hidden)
            );
    }

  
    public function _before_html() {

    	//token
    	if ($this->getAttribute('method') == 'post') {

    		$name = $this->getAttribute('name');
        	$uni = acs_session::getInstance()->createToken($this->getAttribute('name'));

			$this->addHidden('token')->setValue($uni);
			$this->addHidden('token_name')->setValue($name);
    	}

        $table = new acs_table();

        $class = 'form_' . $this->getAttribute('name');
        $table->class = 'acs_form form_' . $this->getAttribute('name');

        $hidden = array();
        $submit = array();



        foreach ($this->_felements as $element) {
        	switch ($element->type) {
        		case 'hidden':
        			$hidden[] = $element->html();
        		break;
        		case 'submit':
        		case 'reset':
        			$submit[] = $element->html();
        		break;
        		default:
		        	if ($this->layout_maxCols < 2) {
		            	$table->add_row((($element->_label) ? $element->_label : null));
		            	$table->add_row($element->html());
					}
					else {
						$table->add_row();
                        $lr = $table->last_row();

                        $class_ele = 'acs_form_td ' . $class . '_element_' . ($element->getAttribute('name') ? $element->getAttribute('name') : null);

		            	$label = (($element->_label) ? $element->_label : null);
		            	$lr->add_tdata($label);
                        $lr->last_td()->class = $class_ele . '_label';

		            	$lr->add_tdata($element->html());
                        $lr->last_td()->class = $class_ele;

                        //$table->last_row()->attritoalltds(array('class' => 'acs_form_td ' . $class . '_td'));
                        $lr->class = 'acs_form_tr ' . $class . '_tr';
					}
        	}
            //if ($label && $element->_labelOnRight) //Extra propertie to let the user choose on wich side the label should appear
                //$data = array_reverse($data);
        }

        if (!empty($submit)) {
        	$table->add_row(implode("\n",$submit));
        }

        return array('elements' => $table->html() . implode("\n",$hidden));
    }
}

class acs_form_output_handler_div {

    private $_output = array();
    private $_output_submit = array();

    private $_outter_div_class = 'acs_form_div_wrapper';
    private $_label_class = 'acs_form_div_label';
    private $_element_id = 'acs_form_%formName%_element_%elementName%';

    private $_element_div_class = 'acs_form_div_element';

    private $_wrapper_suffix = '_wrapper';

    private $_form_div_id = '';
    private $_form_div_class = '';

    private $_fname = null;

    public function __construct($fname) {
        $this->_fname = str_replace(' ','_',$fname);
    }

    /**
    * Wrap the element in the div
    *
    * @param acs_element $element
    * @param bool $nowrap
    */
    public function wrap($element, $nowrap = false) {

        //This is for cases where a hidden element needs to be before a normal element.
        //With this I can just specify that I do not want it to wrapped
        if (!$nowrap) {
            $id_element = $element->getAttribute('name');
            $element_type = ($element->getAttribute('type') ? 'acs_form_' . $element->getAttribute('type') : null);

            $wrapper_id = ($id_element ? $id_element . $this->_wrapper_suffix : null);

            $output = null;

            $outter_div = new acs_div();
            $outter_div->setId($wrapper_id);
            $outter_div->setClass($this->_outter_div_class);

            if ($element->_label) {
                $label_div = new acs_div();

                $label_div->setId($id_element . '_label')
                            ->setClass($this->_label_class);

                //TODO: Create label element


                $outter_div->add($label_div->addThis(new acs_form_label($id_element, $element->_label)));
                //'<label for="' . $id_element . '">' . $element->_label . '</label>'));

            }

            $element_div = new acs_div();
            //$element_div->setId( 'acs_form_' . $this->_fname . '_element_' .$id_element . '_wrapper')
            $element_div->setId( $id_element . '_div')
                            ->setClass($this->_element_div_class);

            $output = $outter_div->addThis($element_div->addThis($element))->html();


             //$outter_div->html();
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
        $form_div = new acs_div();
        $form_div->setId('acs_form_' . $this->_fname);
        $form_div->setClass('acs_form_elements');

        $form_div->add(implode("\n",$this->_output));

        if (!empty($this->_output_submit)) {
            $submitWrapper = new acs_div();
            $submitWrapper->setId('acs_form_submit_' .$this->_fname);
            $submitWrapper->setClass('acs_form_submit_elements');
            $form_div->add($submitWrapper->add(implode("\n",$this->_output_submit)));
        }

        return $form_div->html();
    }
}

class acs_form_output_handler_table {

	private $_elements;
	private $_options;

	public function __construct() {
	}

	public function setElements($elements) {
		$this->_elements = $elements;
	}

	public function setOptions($options) {
		$this->_options = (object)$options;
	}

	public function outputHtml() {
	 	$table = new acs_table();

        $table->class = 'acs_form ';

        $hidden = array();
        $submit = array();

        foreach ($this->_elements as $element) {
        	switch ($element->type) {
        		case 'hidden':
        			$hidden[] = $element->html();
        		break;
        		case 'submit':
        		case 'reset':
        			$submit[] = $element->html();
        		break;
        		default:
		        	if ($this->_options->layout_maxCols < 2) {
		            	$table->add_row((($element->_label) ? $element->_label : null));
		            	$table->add_row($element->html());
					}
					else {
						$table->add_row();
		            	$label = (($element->_label) ? $element->_label : null);
		            	$table->last_row()->add_tdata($label);

		            	$table->last_row()->add_tdata($element->html());
					}
        	}
            //if ($label && $element->_labelOnRight) //Extra propertie to let the user choose on wich side the label should appear
                //$data = array_reverse($data);
        }

        if (!empty($submit)) {
        	$table->add_row(implode("\n",$submit));
        }

        return $table->html() . implode("\n",$hidden);

	}
}

interface acs_form_output_handler {
	public function setElements(array $elements);
	public function setOptions($options);
	public function outputHtml();
}



