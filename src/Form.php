<?php

namespace Lubart\Form;

use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;
use Exception;

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
     * Form ID
     * @var string $id
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
     * List of available in the form groups
     *
     * @var array $groups
     */
    protected $groups = [];

    /**
     * Sing which mark form element as obligation
     *
     * @var string $obligationMark
     */
    protected $obligationMark = '*';

    /**
     * Form constructor.
     *
     * @param string $uri form action
     * @param string $method form method
     */
    public function __construct($uri = "", $method = 'POST') {
        $this->setAction($uri);
        $this->setMethod($method);
    }
    
    /**
     * Add new element to the form
     * 
     * @param FormElement $element new element
     * @return Form
     * @throws Exception
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
            throw new Exception("Element with name '".$element->name()."' already in use");
        }
        
        if($element->type() == "file"){
            $this->files = true;
        }
        
        return $this;
    }

    /**
     * Add created group to the form
     *
     * @param FormGroup $group
     * @return $this
     * @throws Exception
     */
    public function addGroup(FormGroup $group) {
        $group->form = $this;
        
        $this->groups[$group->name()] = $group;
        
        foreach($group->elements() as $element){
            $this->addName($element);
        }
        
        return $this;
    }

    /**
     * Remove specific group from the form
     *
     * @param $name
     * @return Form
     */
    public function removeGroup($name){
        foreach($this->groups[$name]->elements() as $element){
            if (($key = array_search($element->name(), $this->names)) !== false) {
                unset($this->names[$element->name()]);
            }
        }
        
        unset($this->groups[$name]);

        return $this;
    }
    
    /**
     * Count form elements
     * 
     * @return int
     */
    public function count() {
        $count = count($this->elements);

        foreach($this->groups as $group){
            $count += $group->count();
        }

        return $count;
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

        if (($key = array_search($name, $this->names)) !== false) {
            unset($this->names[$key]);
        }
        
        return $this;
    }

    /**
     * Remove element from the form
     *
     * @param FormElement $element
     * @return Form
     */
    public function remove(FormElement $element) {
        return $this->removeElement($element->name());
    }
    
    /**
     * Return all elements in block
     *
     * @deprecated use elements() instead
     * @return array
     */
    public function getElements() {
        return $this->elements();
    }

    /**
     * Return all elements in block
     *
     * @return array
     */
    public function elements() {
        $elements = $this->ungroupedElements();

        foreach($this->groups() as $group){
            $elements += $group->elements();
        }

        return $elements;
    }

    /**
     * Return form elements which are not included to any group
     *
     * @return array;
     */
    public function ungroupedElements() {
        return $this->elements;
    }
    
    /**
     * Return block element
     *
     * @deprecated use element($name) instead
     * @param string $name element name
     * @return FormElement
     */
    public function getElement($name) {
        return $this->element($name);
    }

    /**
     * Return form element by the name
     *
     * @param string $name element name
     * @return FormElement
     */
    public function element($name) {
        return isset($this->elements()[$name])?$this->elements()[$name]:null;
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
     * Include JS logic to the form.
     * 
     * @param string $filePath pathe to the JS file
     */
    public function useJSFile($filePath) {
        $this->jsFile = $filePath;
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
     * @return boolean
     */
    public function js(){
        return $this->js;
    }

    /**
     * Specify id for the form
     *
     * @param $id
     * @return Form
     */
    public function setID($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Return form id
     *
     * @return string
     */
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

    /**
     * Return error bag name
     *
     * @return string
     */
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
     * @return Factory|View
     */
    public function render() {
        return view($this->view, ['form'=>$this]);
    }

    /**
     * Return list of form groups
     *
     * @return array
     */
    public function groups(){
        return $this->groups;
    }

    /**
     * Return specific form group by the name
     *
     * @param string $name group name
     * @return FormGroup
     */
    public function group($name){
        return $this->groups[$name];
    }

    /**
     * Apply specified JavaScript code to rendered form
     *
     * @param string $js JavaScript code
     * @return Form
     */
    public function applyJS($js){
        if(is_null($this->js)){
            $this->js = $js;
        }
        else{
            $this->js .= $js;
        }

        return $this;
    }

    /**
     * List of used names in the form
     *
     * @return array
     */
    public function names(){
        return $this->names;
    }

    /**
     * Note element name and control if it is unique
     *
     * @param FormElement $element
     * @throws Exception
     * @return Form
     */
    public function addName(FormElement $element){
        if (!in_array($element->name(), $this->names)) {
            $this->names[] = $element->name();
        } elseif (!in_array($element->type(), ['checkbox', 'radio'])) {
            throw new Exception("Element with name '" . $element->name() . "' already in use");
        }

        if ($element->type() == "file") {
            $this->files = true;
        }
        
        return $this;
    }

    /**
     * Return obligation mark for the form element
     *
     * @return string
     */
    public function obligationMark() {
        return $this->obligationMark;
    }

    /**
     * Set obligation mark for the form element
     *
     * @param $mark
     * @return Form
     */
    public function setObligationMark($mark) {
        $this->obligationMark = $mark;

        return $this;
    }

    /**
     * Return form data in JSON format
     *
     * @return false|string
     */
    public function toJson() {
        $groups = [];
        $unGroupedElements = [];

        foreach ($this->groups() as $group){
            $gr = new \stdClass();
            $gr->name = $group->name();
            $gr->label = $group->label();
            $gr->parameters = empty($group->parameters()) ? new \stdClass() : $group->parameters();
            $grElements = [];

            foreach ($group->elements() as $element){
                $grElements[] = $this->elementToJson($element);
            }

            $gr->elements = $grElements;

            $groups[] = $gr;
        }

        foreach ($this->ungroupedElements() as $element){
            $unGroupedElements[] = $this->elementToJson($element);
        }

        $json = new \stdClass();
        $json->action = $this->action();
        $json->groups = $groups;
        $json->unGrouppedElements = $unGroupedElements;
        $json->isUploadFile = $this->files();
        $json->method = $this->method();
        $json->id = $this->id();
        $json->errorBag = $this->errorBag();
        $json->jsFile = $this->jsFile();
        $json->js = $this->js();

        return json_encode($json);
    }

    /**
     * Build proper JSON format from the element data
     *
     * @param FormElement $element
     * @return \stdClass
     */
    protected function elementToJson(FormElement $element) {
        $el = new \stdClass();
        $el->name = $element->name();
        $el->label = $element->label();
        $el->type = $element->type();
        $el->value = $element->value();
        $el->options = $element->options();
        $el->parameters = empty($element->parameters()) ? new \stdClass() : $element->parameters();
        $el->checked = $element->check();
        $el->isObligatory = $element->isObligatory();
        $el->obligationMark = $element->obligationMark();

        return $el;
    }
}
