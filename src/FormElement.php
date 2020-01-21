<?php

namespace Lubart\Form;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

/**
 * Class describes different form elements
 * @package Lubart\Form
 *
 * @method static FormElement text(array $parameters = []) builds input text form element
 * @method static FormElement textarea(array $parameters = []) builds textarea form element
 * @method static FormElement password(array $parameters = []) builds input password form element
 * @method static FormElement email(array $parameters = []) builds input email form element
 * @method static FormElement file(array $parameters = []) builds input file form element
 * @method static FormElement checkbox(array $parameters = []) builds checkbox form element
 * @method static FormElement radio(array $parameters = []) builds radio form element
 * @method static FormElement number(array $parameters = []) builds input number form element
 * @method static FormElement select(array $parameters = []) builds select form element
 * @method static FormElement selectRange(array $parameters = []) builds select form element
 * @method static FormElement hidden(array $parameters = []) builds hidden form element
 * @method static FormElement button(array $parameters = []) builds button form element
 * @method static FormElement submit(array $parameters = []) builds submit form element
 * @method static FormElement html(array $parameters = []) inserts html code into the form
 * @method static FormElement date(array $parameters = []) builds input date form element
 * @method static FormElement time(array $parameters = []) builds input time form element
 */
class FormElement {
    
    /**
     * Element name
     * 
     * @var string $name
     */
    protected $name = 'submit';
    
    /**
     * Element label
     * 
     * @var string $label
     */
    protected $label = '';
    
    /**
     * Element type
     * 
     * @var string $type
     */
    protected $type = 'text';
    
    /**
     * Element value
     * 
     * @var string|array $value 
     */
    protected $value;
    
    /**
     * Options for select, checkbox or radio-box elements
     * 
     * @var array $options
     */
    protected $options = [];
    
    /**
     * Additional element parameters
     * 
     * @var array $parameters
     */
    protected $parameters = [];
    
    /**
     * Show is checkbox or radiobox checked
     * 
     * @var boolean $checked
     */
    protected $checked = false;
    
    /**
     * Show is field is obligatory
     * 
     * @var boolean $isObligatory
     */
    protected $isObligatory = false;

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

    /**
     * Available element types
     *
     * @var array $availableTypes
     */
    protected $availableTypes = ['text', 'textarea', 'password', 'email', 'file', 'checkbox', 'radio', 'number', 'select', 'hidden', 'button', 'submit', 'html', 'date', 'time'];

    /**
     * Sing which mark element as obligation.
     * Default value set in the Form class.
     *
     * @var string $obligationMark
     */
    protected $obligationMark = null;

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

    /**
     * FormElement constructor.
     *
     * @param array $input parameters list
     */
    public function __construct(array $input = []) {
        $this->setType($input['type']);
        foreach($input as $key=>$val){
            if(method_exists($this, $key)){
                $this->{'set'.ucfirst($key)}($val);
            }
            else{
                $this->setParameter($val, $key);
            }
        }
    }

    /**
     * Return element name
     *
     * @return string
     */
    public function name() {
        return $this->name;
    }

    /**
     * Set element name
     *
     * @param string $name element name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        
        return $this;
    }

    /**
     * Return element label
     *
     * @return string
     */
    public function label() {
        return $this->label;
    }

    /**
     * Set element label
     *
     * @param string $label element label
     * @return $this
     */
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

    /**
     * Change element type
     *
     * @param string $type element type, it should be mentioned in the $availableTypes list
     * @return $this
     */
    public function setType($type) {
        if(in_array($type, $this->availableTypes)){
            $this->type = $type;
        }
        
        return $this;
    }

    /**
     * Return element value
     *
     * @return array|string
     */
    public function value() {
        return $this->value;
    }

    /**
     * Set element value.
     * Value cannot be set for password and file types
     *
     * @param string $value element value
     * @return $this
     */
    public function setValue($value) {
        if(!in_array($this->type(), ['password', 'file'])) {
            $this->value = $value;
        }

        return $this;
    }

    /**
     * Return options for select element
     *
     * @return array|null
     */
    public function options() {
        if($this->type() == 'select') {
            return $this->options;
        }

        return null;
    }

    /**
     * Set options to select element
     *
     * @param array $options list of options in format key=>value
     * @return $this
     */
    public function setOptions($options) {
        if($options instanceof \Illuminate\Support\Collection){
            $options = $options->toArray();
        }
        if($this->type() == 'select') {
            $this->options = $options;
        }

        return $this;
    }

    /**
     * Add new option to the select element
     *
     * @param string $key option name
     * @param string $value option value
     * @return $this
     */
    public function addOption($key, $value) {
        if($this->type() == 'select'){
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * Return all element parameters
     *
     * @return array
     */
    public function parameters() {
        return $this->parameters;
    }

    /**
     * Set specific parameter value
     *
     * @param string $parameter parameter value
     * @param string $key parameter name
     * @return $this
     */
    public function setParameter($parameter, $key) {
        $this->parameters[$key] = $parameter;
                
        return $this;
    }

    /**
     * Return specific parameter value
     *
     * @param string $key parameter name
     * @return mixed|null
     */
    public function parameter($key) {
        switch($key){
            case "name":
                return $this->name();
                break;
            case "value":
                return $this->value();
                break;
        }

        return $this->parameters[$key] ?? null;
    }

    /**
     * Remove specified parameter
     *
     * @param string $key parameter name
     * @return $this
     */
    public function removeParameter($key) {
        unset($this->parameters[$key]);

        return $this;
    }

    /**
     * Tick or un-tick checkbox or radio button element
     *
     * @param boolean $check element is ticked if value is TRUE
     * @return $this
     */
    public function setCheck($check) {
        if(in_array($this->type, ['checkbox', 'radio'])){
            $this->checked = (boolean)$check;
        }
        
        return $this;
    }

    /**
     * Check if checkbox or radio button is ticked
     *
     * @return bool
     */
    public function isChecked() {
        if(in_array($this->type, ['checkbox', 'radio'])) {
            return $this->checked;
        }

        return false;
    }

    /**
     * Tick checkbox or radio button element
     *
     * @return FormElement
     */
    public function check() {
        return $this->setCheck(true);
    }

    /**
     * Un-tick checkbox or radio button element
     *
     * @return FormElement
     */
    public function uncheck() {
        return $this->setCheck(false);
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
     * Check is field obligatory to use
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
     * @return FormElement
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
     * @return FormElement
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
     * Return obligation mark for the element
     *
     * @return string
     */
    public function obligationMark() {
        return $this->obligationMark ?? ($this->form ? $this->form->obligationMark() : '*');
    }

    /**
     * Set obligation mark for the element
     *
     * @param $mark
     * @return FormElement
     */
    public function setObligationMark($mark) {
        $this->obligationMark = $mark;

        return $this;
    }
    
    /**
     * Render element html view
     * 
     * @return Factory|View
     */
    public function render() {
        return view('lubart.form::element', ['element'=>$this]);
    }
}
