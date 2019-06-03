<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lubart\Form;

use Illuminate\Support\Facades\View;

/**
 * Group of few elements
 *
 * @author lubart
 */
class FormGroup {
    
    /**
     * Group name
     * 
     * @var string $name
     */
    private $name;
    
    /**
     * Related form
     * 
     * @var Form $form
     */
    protected $form;
    
    /**
     * Element names in the group. Helps identify element
     * 
     * @var array $names
     */
    protected $names = [];
    
    /**
     * Group label
     * 
     * @var string $label
     */
    protected $label = null;
    
    /**
     * Group additional parameters
     * 
     * @var array $parameters
     */
    protected $parameters = [];
    
    /**
     * Elements of the group
     * 
     * @var array $elements
     */
    protected $elements = [];

    public function __construct($name, $label = null, $parameters = []) {
        $this->name = $name;
        $this->setLabel($label);
        foreach ($parameters as $key=>$parameter){
            $this->setParameter($parameter, $key);
        }
    }
    
    public function name(){
        return $this->name;
    }

    public function label() {
        return $this->label;
    }
    
    public function setLabel($label) {
        $this->label = $label;
        
        return $this;
    }
    
    public function parameters() {
        return $this->parameters;
    }
    
    public function setParameter($parameter, $key) {
        $this->parameters[$key] = $parameter;
                
        return $this;
    }

    public function removeParameter($key) {
        unset($this->parameters[$key]);

        return $this;
    }
    
    /**
     * Set related form
     *
     * @param Form $form
     * @return Form
     */
    public function setForm(Form $form) {
        $this->form = $form;

        return $this;
    }

    /**
     * Get related form
     *
     * @return Form
     */
    public function form() {
        return $this->form;
    }
    
    /**
     * Add new element to the form
     * 
     * @param FormElement $element new element
     * @return $this
     * @throws \Exception
     */
    public function add(FormElement $element) {
        $element->setGroup($this);
        
        if(is_null($this->form)){
            if (!in_array($element->name(), $this->names)) {
                $this->elements[$element->name()] = $element;
                $this->names[] = $element->name();
            } elseif (in_array($element->type(), ['checkbox', 'radio'])) {
                $this->elements[$element->name() . '_' . $this->count()] = $element;
            } else {
                throw new \Exception("Element with name '" . $element->name() . "' already in use");
            }
        }
        else{
            if (!in_array($element->name(), $this->form->names())) {
                $this->elements[$element->name()] = $element;
                $this->form->addName($element);
                $this->names[] = $element->name();
            } elseif (in_array($element->type(), ['checkbox', 'radio'])) {
                $this->elements[$element->name() . '_' . $this->count()] = $element;
            } else {
                throw new \Exception("Element with name '" . $element->name() . "' already in use");
            }
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

        if (($key = array_search($name, $this->form()->names)) !== false) {
                unset($this->form()->names[$key]);
        }
        
        return $this;
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
     * Render form view
     * 
     * @return type
     */
    public function render() {
        return View::make('lubart::group', ['group'=>$this]);
    }
}
