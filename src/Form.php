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
     * Element names in the form. Helps identify element
     * 
     * @var array $names
     */
    protected $names = [];
    
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
    protected $files = false;
    
    /**
     * Form method
     * 
     * @var string 
     */
    private $method = "POST";

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
    
    /**
     * Shows is JS logic used
     * 
     * @var boolean $isJS
     */
    protected $isJS = false;
    
    /**
     * Path to JS file with related logic
     * 
     * @var string  $jsFile
     */
    protected $jsFile = null;


    /**
     * JS code related to the form
     * 
     * @var String $js
     */
    protected $js = null;


    /**
     * Form type. Available values are 'setup', 'settings', 'public'
     * 
     * @var string $type 
     */
    protected $type = 'settings';
    
    protected $groups = [];

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
    
    public function addGroup(FormGroup $group) {
        $group->setForm($this);
        
        $this->groups[$group->name()] = $group;
        
        foreach($group->getElements() as $element){
            $this->addName($element);
        }
        
        return $this;
    }
    
    public function removeGroup($name){
        foreach($this->groups[$name]->getElements() as $element){
            if (($key = array_search($element->name(), $this->names)) !== false) {
                unset($this->names[$element->name()]);
            }
        }
        
        unset($this->groups[$name]);
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
        
        return $this;
    }

    /**
     * Set action URI of the form
     *
     * @param string $method
     * @return string
     */
    public function setMethod($method) {
            $this->method = $method;

            return $this;
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
        $this->isOpen = true;

        return $this;
    }
    
    /**
     * Make form hidden
     * 
     * @return boolean
     */
    public function close() {
        $this->isOpen = false;

        return $this;
    }
    
    /**
     * Include JS losic to the form.
     * 
     * @param string $filePath pathe to the JS file
     */
    public function useJSFile($filePath) {
        $this->isJS = true;
        $this->jsFile = $filePath;
    }
    
    /**
     * Return is JS logic used. JS file can be found with jsFile() method
     * 
     * @return boolean
     */
    public function isJS() {
        return $this->isJS;
    }
    
    /**
     * Return path to the JS file with related logic
     * 
     * @return string
     */
    public function jsFile(){
        return $this->jsFile;
    }

        /**
     * Return JS code related to the form
     * 
     * @return type
     */
    public function js(){
        return $this->js;
    }

    /**
     * Set form type
     * 
     * @param string $type
     * @return string
     */
    public function setType($type) {
        return $this->type = $type;
    }
    
    /**
     * Return form type
     * 
     * @return string
     */
    public function type() {
        return $this->type;
    }

    public function setID($id) {
        $this->id = $id;

        return $this;
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
        $this->errorBag = $name;

        return $this;
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
        $this->view = $view;

        return $this;
    }

    /**
     * Get blade template name
     *
     * @return string
     */
    public function view() {
        return $this->view;
    }
    
    /**
     * Render form view
     * 
     * @return type
     */
    public function render() {
        return View::make($this->view, ['form'=>$this]);
    }
    
    public function groups(){
        return $this->groups;
    }
    
    public function group($name){
        return $this->groups[$name];
    }
    
    public function applyJS($js){
        if(is_null($this->js)){
            $this->js = $js;
        }
        else{
            $this->js .= $js;
        }
        
        return $this;
    }
    
    public function names(){
        return $this->names;
    }
    
    public function addName($element){
        if (!in_array($element->name(), $this->names)) {
            $this->names[] = $element->name();
        } elseif (!in_array($element->type(), ['checkbox', 'radio'])) {
            throw new \Exception("Element with name '" . $element->name() . "' already in use");
        }

        if ($element->type() == "file") {
            $this->files = true;
        }
        
        return;
    }
}
