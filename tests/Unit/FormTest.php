<?php
/**
 * @author Viacheslav Dymarchuk
 */

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;

class FormTest extends TestCase {

    use WithFaker;

    /** @test */
    function it_checks_default_values_for_the_empty_form(){
        $form = new Form();

        $this->assertEquals(0, $form->count());
        $this->assertEmpty($form->elements());
        $this->assertEmpty($form->names());
        $this->assertEquals("", $form->action());
        $this->assertFalse($form->files());
        $this->assertEquals('POST', $form->method());
        $this->assertEmpty($form->id());
        $this->assertEquals('default', $form->errorBag());
        $this->assertEquals('lubart.form::form', $form->view());
        $this->assertNull($form->jsFile());
        $this->assertNull($form->js());
        $this->assertEmpty($form->groups());
    }

    /** @test */
    function it_adds_text_element_to_the_form(){
        $form = new Form();

        $form->add($element = FormElement::text(['name'=> $name = $this->faker->word]));

        $this->assertEquals(1, $form->count());
        $this->assertCount(1, $form->elements());
        $this->assertCount(1, $form->names());
        $this->assertEquals($element, $form->element($name));
    }

    /** @test */
    function it_adds_checkbox_element_to_the_form(){
        $form = new Form();
        $form->add($element = FormElement::checkbox(['name'=> $name = $this->faker->word]));

        $this->assertEquals(1, $form->count());
        $this->assertCount(1, $form->elements());
        $this->assertCount(1, $form->names());
        $this->assertEquals($element, $form->element($name));
    }

    /** @test */
    function exception_should_be_thrown_on_adding_element_with_existing_name(){
        $form = new Form();

        $form->add(FormElement::text(['name'=> $name = $this->faker->word]));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Element with name '".$name."' already in use");

        $form->add(FormElement::text(['name'=> $name]));
    }

    /** @test */
    function is_adds_two_checkboxes_with_the_same_name_to_the_form(){
        $form = new Form();
        $form->add($element = FormElement::checkbox(['name'=> $name = $this->faker->word . '[]']));
        $form->add($secondElement = FormElement::checkbox(['name'=> $name]));

        $this->assertEquals(2, $form->count());
        $this->assertCount(2, $form->elements());
        $this->assertCount(1, $form->names());
        $this->assertEquals($element, $form->element($name));
        $this->assertEquals($secondElement, $form->element($name));
    }

    /** @test */
    function it_adds_file_element_to_the_form(){
        $form = new Form();

        $form->add($element = FormElement::file(['name'=> $name = $this->faker->word]));

        $this->assertTrue($form->files());
    }

    /** @test */
    function it_removes_element_from_the_form(){
        $form = new Form();

        $form->add($element = FormElement::text(['name'=> $name = $this->faker->word]));

        $this->assertEquals(1, $form->count());

        $form->remove($element);

        $this->assertEquals(0, $form->count());
        $this->assertCount(0, $form->elements());
        $this->assertCount(0, $form->names());
        $this->assertNull($form->element($name));
    }

    /** @test */
    function it_tests_aliases_getElements_and_elements_methods(){
        $form = new Form();

        $form->add($element = FormElement::text(['name'=> $name = $this->faker->word]));

        $this->assertEquals($form->getElements(), $form->elements());
    }

    /** @test */
    function it_tests_aliases_getElement_and_element_methods(){
        $form = new Form();

        $form->add($element = FormElement::text(['name'=> $name = $this->faker->word]));

        $this->assertEquals($form->getElement($name), $form->element($name));
    }

    /** @test */
    function it_connects_javascript_file_to_the_form(){
        $form = new Form();

        $form->useJSFile('script.js');

        $this->assertNotNull($form->jsFile());
    }

    /** @test */
    function is_sets_form_id(){
        $form = new Form();

        $form->setID($id = $this->faker->word);

        $this->assertEquals($id, $form->id());
    }

    /** @test */
    function it_sets_custom_error_bag(){
        $form = new Form();

        $form->setErrorBag($bag = $this->faker->word);

        $this->assertEquals($bag, $form->errorBag());
    }

    /** @test */
    function it_shows_that_javascript_file_is_rendered(){
        $form = new Form();

        $form->useJSFile('script.js');

        $this->assertContains("<script src='script.js' />", $form->render()->render());
    }

    /** @test */
    function it_renders_custom_javasript_code(){
        $form = new Form();

        $form->applyJS("console.log('JavaScript works!')");

        $this->assertContains("console.log('JavaScript works!')", $form->render()->render());
    }

    /** @test */
    function it_adds_few_lines_of_custom_javasript_code(){
        $form = new Form();

        $form->applyJS("console.log('JavaScript works!')");
        $form->applyJS("console.log('Form is rendered!')");

        $this->assertContains("console.log('JavaScript works!')", $form->render()->render());
        $this->assertContains("console.log('Form is rendered!')", $form->render()->render());
    }

    /** @test */
    function it_changes_view_template(){
        $form = new Form();

        $form->setView($view = $this->faker->word);

        $this->assertEquals($view, $form->view());
    }

    /** @test */
    function it_adds_groups_to_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $form->addGroup($group);

        $this->assertCount(1, $form->groups());
        $this->assertCount(0, $form->elements());
    }

    /** @test */
    function it_adds_not_empty_group_to_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $group->add(FormElement::text(['name' => $name = $this->faker->word]));

        $form->addGroup($group);

        $this->assertCount(1, $form->groups());
        $this->assertCount(1, $group->elements());
        $this->assertEquals(1, $form->count());
        $this->assertEquals($group, $form->group($groupName));
    }

    /** @test */
    function it_adds_element_to_the_group_inside_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $form->addGroup($group);

        $group->add(FormElement::text(['name' => $name = $this->faker->word]));

        $this->assertCount(1, $form->groups());
        $this->assertCount(1, $group->elements());
        $this->assertEquals(1, $form->count());
    }

    /** @test */
    function it_adds_checkboxes_with_the_same_name_to_the_group(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $group->add(FormElement::checkbox(['name' => $name = $this->faker->word . "[]"]));
        $group->add(FormElement::checkbox(['name' => $name]));

        $form->addGroup($group);

        $this->assertCount(1, $form->groups());
        $this->assertCount(2, $group->elements());
        $this->assertEquals(2, $form->count());
    }

    /** @test */
    function it_adds_file_to_the_group(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $group->add(FormElement::file(['name' => $name = $this->faker->word]));

        $form->addGroup($group);

        $this->assertTrue($form->files());
    }

    /** @test */
    function it_adds_two_elements_with_the_same_name_to_the_group_inside_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $this->expectException(Exception::class);

        $form->addGroup($group);

        $group->add(FormElement::text(['name' => $name = $this->faker->word]));
        $group->add(FormElement::text(['name' => $name]));
    }

    /** @test */
    function it_adds_element_to_the_group_with_name_which_exists_in_the_form(){
        $form = new Form();

        $form->add(FormElement::text(['name' => $name = $this->faker->word]));

        $group = new FormGroup($groupName = $this->faker->word);

        $this->expectException(Exception::class);

        $group->add(FormElement::text(['name' => $name]));

        $form->addGroup($group);
    }

    /** @test */
    function is_removes_group_from_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $form->addGroup($group);

        $this->assertCount(1, $form->groups());

        $form->removeGroup($groupName);

        $this->assertCount(0, $form->groups());
    }

    /** @test */
    function is_removes_group_together_with_elements_from_the_form(){
        $form = new Form();
        $group = new FormGroup($groupName = $this->faker->word);

        $group->add(FormElement::text(['name' => $name = $this->faker->word]));
        $group->add(FormElement::text(['name' => $name = $this->faker->word]));

        $form->addGroup($group);

        $this->assertCount(1, $form->groups());
        $this->assertCount(2, $group->elements());
        $this->assertEquals(2, $form->count());

        $form->removeGroup($groupName);

        $this->assertCount(0, $form->groups());
        $this->assertEquals(0, $form->count());
    }

    /** @test - it sets obligation mark to the form */
    function it_sets_obligation_mark_to_the_form() {
        $form = new Form();
        $form->add($element = FormElement::text(['name'=>'text', 'label'=>'Label'])->obligatory());
        $form->setObligationMark('#');

        $this->assertContains('<span class="lubart-form__obligatoryMark">#</span>', $element->render()->render());
    }

    /** @test - it returns form data in json format */
    function it_returns_form_data_in_json_format() {
        $form = new Form($url = $this->faker->url);

        $form->add(FormElement::text(['name' => $textName = $this->faker->word]));

        $group = new FormGroup($groupName = $this->faker->word, $groupLabel = $this->faker->word);

        $group->add(FormElement::email(['name' => $emailName = $this->faker->word]));

        $form->addGroup($group);

        $json = json_decode($form->toJson());

        $this->assertEquals($url, $json->action);
        $this->assertCount(1, $json->groups);
        $this->assertEquals($groupName, $json->groups[0]->name);
        $this->assertEquals($groupLabel, $json->groups[0]->label);
        $this->assertCount(1, $json->groups[0]->elements);
        $this->assertEquals($emailName, $json->groups[0]->elements[0]->name);
        $this->assertCount(1, $json->unGrouppedElements);
        $this->assertEquals($textName, $json->unGrouppedElements[0]->name);
    }
}
