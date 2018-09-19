<?php

namespace Lubart\Form;

use Illuminate\Support\Facades\View;

class Form {
    
    /**
     * Set of elements
     * 
     * @var array $elements
     */
    private $elements = [];
    
    /**
     * Element names in the block. Helps identify element
     * 
     * @var array $names
     */
    private $names = [];
    
    /**
     * Form action URI
     * 
     * @var string $action
     */
    private $action;
    
    /**
     * Is file field is used in the form
     * 
     * @var boolean $files 
     */
    private $files = false;
    
    /**
     * Form method
     * 
     * @var string 
     */
    private $method = "POST";

	/**
	 * Shows is form has advanced parameters
	 *
	 * @var boolean $isOpen
	 */
	protected $isAdvanced = false;
    
    /**
     * Shows is form visible
     * 
     * @var boolean $isOpen 
     */
    protected $isOpen = false;

	/**
	 * Form ID
	 * @var string $form
	 */
    private $id;

	/**
	 * Error bag name used by validator
	 *
	 * @var string $errorBag
	 */
	protected $errorBag = 'default';

	/**
	 * View blade template
	 *
	 * @var string $view
	 */
	protected $view = 'lubart.form::form';

    public function __construct($uri = "", $method = 'POST') {
        $this->setAction($uri);
        $this->setMethod($method);
    }
    
    /**
     * Add new element to the form
     * 
     * @param FormElement $element new element
     * @return $this
     * @throws \Exception
     */
    public function add(FormElement $element) {
    	$element->setForm($this);

        if(!in_array($element->name(), $this->names)){
            $this->elements[$element->name()] = $element;
            $this->names[] = $element->name();
        }
        elseif(in_array($element->type(), ['checkbox', 'radio'])){
            $this->elements[$element->name().'_'. $this->count()] = $element;
        }
        else{
            throw new \Exception("Element with name '".$element->name()."' already in use");
        }
        
        if($element->type() == "file"){
            $this->files = true;
        }
        
        return $this;
    }
    
    /**
     * Count form elements
     * 
     * @return int
     */
    public function count() {
        return count($this->elements);
    }
    
    /**
     * Remove element from the form
     * 
     * @param string $name element name
     * @return $this
     */
    public function remove($name) {
        if(isset($this->elements[$name])){
            unset($this->elements[$name]);
        }

		if (($key = array_search($name, $this->names)) !== false) {
			unset($this->names[$key]);
		}
        
        return $this;
    }
    
    /**
     * Show is advanced search used
     * 
     * @return boolean
     */
    public function isAdvanced() {
        return $this->isAdvanced;
    }
    
    /**
     * Set value for advanced search
     * 
     * @param boolean $val
     * @return boolean
     */
    public function setAdvanced($val) {
        $this->isAdvanced = $val;
        
        return $this->isAdvanced;
    }
    
    /**
     * Return all elements in block
     * 
     * @return array
     */
    public function getElements() {
        return $this->elements;
    }
    
    /**
     * Return block element
     * 
     * @return FormElement
     */
    public function getElement($name) {
        return isset($this->elements[$name])?$this->elements[$name]:null;
    }
    
    /**
     * Get form action URI
     * 
     * @return string
     */
    public function action() {
        return $this->action;
    }
    
    /**
     * Get form method
     * 
     * @return string
     */
    public function method() {
        return $this->method;
    }
    
    /**
     * Is file field is used in the form
     * 
     * @return boolean
     */
    public function files() {
        return $this->files;
    }
    
    /**
     * Set action URI of the form
     * 
     * @param string $uri
     * @return string
     */
    public function setAction($uri) {
        $this->action = $uri;
        
        return $this->action;
    }

	/**
	 * Set action URI of the form
	 *
	 * @param string $method
	 * @return string
	 */
	public function setMethod($method) {
		$this->method = $method;

		return $this->method;
	}
    
    /**
     * Check is form visible
     * 
     * @return boolean
     */
    public function isOpen() {
        return $this->isOpen;
    }
    
    /**
     * Make form visible
     * 
     * @return boolean
     */
    public function open() {
        return $this->isOpen = true;
    }
    
    /**
     * Make form hidden
     * 
     * @return boolean
     */
    public function close() {
        return $this->isOpen = false;
    }

	public function setID($id) {
		return $this->id = $id;
    }

	public function id() {
		return $this->id;
    }

	/**
	 * Set error bag name
	 *
	 * @param string $name
	 * @return string
	 */
	public function setErrorBag($name) {
		return $this->errorBag = $name;
	}

	public function errorBag() {
		return $this->errorBag;
	}

	/**
	 * Set custom view blade template
	 *
	 * @param $view
	 * @return mixed
	 */
	public function setView($view) {
		return $this->view = $view;
	}

	/**
	 * Get blade template name
	 *
	 * @return string
	 */
	public function view() {
		return $this->view;
	}

	public function includeView() {
		return View::make($this->view, ['form'=>$this]);
	}
}
