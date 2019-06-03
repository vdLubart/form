<?php

namespace Lubart\Form;

use Illuminate\Support\Facades\View;

class FormElement {
    
    /**
     * Element name
     * 
     * @var string $name
     */
    private $name = 'submit';
    
    /**
     * Element label
     * 
     * @var string $label
     */
    private $label = '';
    
    /**
     * Element type
     * 
     * @var string $type
     */
    private $type = 'text';
    
    /**
     * Element value
     * 
     * @var string|array $value 
     */
    private $value;
    
    /**
     * Options for select, checkbox or radiobox elements
     * 
     * @var array $options
     */
    private $options = [];
    
    /**
     * Additional element parameters
     * 
     * @var array $parameters
     */
    private $parameters = [];
    
    /**
     * Show is checkbox or radiobox checked
     * 
     * @var boolean $checked
     */
    private $checked = false;
    
    /**
     * Show is field is obligatory
     * 
     * @var boolean $isObligatory
     */
    protected $isObligatory = true;

    /**
     * Form related to the element
     *
     * @var Form $form
     */
    protected $form;
    
    /**
     * Group related to the element
     * 
     * @var FormGroup $group
     */
    protected $group = null;

    private $availableTypes = ['text', 'textarea', 'password', 'email', 'file', 'checkbox', 'radio', 'number', 'select', 'selectRange', 'selectMonth', 'hidden', 'button', 'submit', 'html', 'date', 'time'];

    /**
     * Define form element
     *
     * @param $name
     * @param array $arguments
     * @return FormElement
     */
    public static function __callStatic($name, $arguments = []) {
        $arguments[0]['type'] = $name;
        return new FormElement($arguments[0]);
    }
    
    public function __construct(array $input = []) {
        $this->setType($input['type']);
        foreach($input as $key=>$val){
            if(method_exists($this, $key)){
                $this->{'set'.ucfirst($key)}($val);
            }
            else{
                $this->setParameters($val, $key);
            }
        }
    }
    
    public function name() {
        return $this->name;
    }
    
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }
    
    public function label() {
        return $this->label;
    }
    
    public function setLabel($label) {
        $this->label = $label;
        
        return $this;
    }
    
    /**
     * Return element type
     * 
     * @return string
     */
    public function type() {
        return $this->type;
    }
    
    public function setType($type) {
        if(in_array($type, $this->availableTypes)){
            $this->type = $type;
        }
        
        return $this;
    }
    
    public function value() {
        return $this->value;
    }
    
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }
    
    public function options() {
        return $this->options;
    }
    
    public function setOptions($option, $key=null) {
        if(!is_null($key)){
            $this->options[$key] = $option;
        }
        else{
            $this->options = $option;
        }
                
        return $this;
    }
    
    public function parameters() {
        return $this->parameters;
    }
    
    public function setParameters($parameter, $key) {
        $this->parameters[$key] = $parameter;
                
        return $this;
    }

    public function removeParameter($key) {
        unset($this->parameters[$key]);

        return $this;
    }
    
    public function setCheck($check) {
        if(in_array($this->type, ['checkbox', 'radio'])){
            $this->checked = (boolean)$check;
        }
        
        return $this;
    }
    
    public function check() {
        return $this->checked;
    }
    
    /**
     * Make field not obligatory to use
     * 
     * @return $this
     */
    public function notObligatory() {
        $this->isObligatory = false;
        
        return $this;
    }
    
    /**
     * Make field obligatory to use
     * 
     * @return $this
     */
    public function obligatory() {
        $this->isObligatory = true;
        
        return $this;
    }
    
    /**
     * Show is field obligatory to use
     * 
     * @return boolean
     */
    public function isObligatory() {
        return $this->isObligatory;
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
     * Set related group
     *
     * @param FormGroup $group
     * @return FormGroup
     */
    public function setGroup(FormGroup $group) {
        $this->group = $group;

        return $this;
    }

    /**
     * Get related group
     *
     * @return FormGroup
     */
    public function group() {
        return $this->group;
    }
    
    /**
     * Render element html view
     * 
     * @return type
     */
    public function render() {
        return View::make('lubart.form::element', ['element'=>$this]);
    }
}
