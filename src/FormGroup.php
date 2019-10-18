<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Lubart\Form;

use Illuminate\Contracts\View\Factory;
use \Illuminate\View\View;
use Exception;

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
     * Related form.
     * Should be public to connect Group to the Form and Form to the Group
     * 
     * @var Form $form
     */
    public $form;
    
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

    /**
     * FormGroup constructor.
     * @param $name
     * @param null $label group label
     * @param array $parameters fieldset custom parameters
     */
    public function __construct($name, $label = null, $parameters = []) {
        $this->name = $name;
        $this->setLabel($label);
        foreach ($parameters as $key=>$parameter){
            $this->setParameter($parameter, $key);
        }
    }

    /**
     * Return group name
     *
     * @return string
     */
    public function name(){
        return $this->name;
    }

    /**
     * Return group label
     *
     * @return string
     */
    public function label() {
        return $this->label;
    }

    /**
     * Set group label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;
        
        return $this;
    }

    /**
     * Return fieldset custom parameters
     *
     * @return array
     */
    public function parameters() {
        return $this->parameters;
    }

    /**
     * Return fieldset custom parameter value
     *
     * @param string $key
     * @return mixed|null
     */
    public function parameter($key) {
        return $this->parameters[$key] ?? null;
    }

    /**
     * Set fieldset custom parameters
     *
     * @param $parameter
     * @param $key
     * @return $this
     */
    public function setParameter($parameter, $key) {
        $this->parameters[$key] = $parameter;
                
        return $this;
    }

    /**
     * Remove fieldset custom parameter
     *
     * @param $key
     * @return $this
     */
    public function removeParameter($key) {
        unset($this->parameters[$key]);

        return $this;
    }

    /**
     * Set related form
     *
     * @param Form $form
     * @return $this
     * @throws Exception
     */
    public function setForm(Form $form) {
        $form->addGroup($this);

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
     * @return FormGroup
     * @throws Exception
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
                throw new Exception("Element with name '" . $element->name() . "' already in use");
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
                throw new Exception("Element with name '" . $element->name() . "' already in use");
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
     * Remove element from the form by the element name
     * 
     * @param string $name element name
     * @return $this
     */
    public function removeElement($name) {
        if(isset($this->elements[$name])){
            unset($this->elements[$name]);
        }

        if(!is_null($this->form())) {
            if (($key = array_search($name, $this->form()->names())) !== false) {
                unset($this->form()->names()[$key]);
            }
        }
        
        return $this;
    }

    /**
     * Remove element from the form
     *
     * @param FormElement $element
     * @return FormGroup
     */
    public function remove(FormElement $element) {
        return $this->removeElement($element->name());
    }
    
    /**
     * Return all elements in the group
     *
     * @deprecated use elements() instead
     * @return array
     */
    public function getElements() {
        return $this->elements();
    }

    /**
     * Return all elements in the group
     *
     * @return array
     */
    public function elements() {
        return $this->elements;
    }
    
    /**
     * Return group element
     *
     * @deprecated use element($name) instead
     * @param string $name element name
     * @return FormElement
     */
    public function getElement($name) {
        return $this->element($name);
    }

    /**
     * Return group element
     *
     * @param string $name element name
     * @return FormElement
     */
    public function element($name) {
        return isset($this->elements[$name])?$this->elements[$name]:null;
    }
    
    /**
     * Render form view
     * 
     * @return Factory|View
     */
    public function render() {
        return view('lubart.form::group', ['group'=>$this]);
    }
}
