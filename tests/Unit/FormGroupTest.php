<?php
/**
 * @author Viacheslav Dymarchuk
 */

use Lubart\Form\FormGroup;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Form\FormElement;
use Lubart\Form\Form;

class FormGroupTest extends TestCase {

    use WithFaker;

    /** @test */
    function it_builds_group_with_default_data() {
        $group = new FormGroup($name = $this->faker->word);

        $this->assertEquals($name, $group->name());
        $this->assertNull($group->form());
        $this->assertNull($group->label());
        $this->assertEmpty($group->parameters());
        $this->assertCount(0, $group->elements());
    }

    /** @test - it adds element to the form group */
    function it_adds_element_to_the_form_group() {
        $group = new FormGroup($name = $this->faker->word);

        $this->assertCount(0, $group->elements());

        $element = FormElement::text(['name' => $elementName = $this->faker->word]);

        $group->add($element);

        $this->assertCount(1, $group->elements());
    }

    /** @test - it adds two elements to the form group */
    function it_adds_two_elements_to_the_form_group() {
        $group = new FormGroup($name = $this->faker->word);

        $group->add(FormElement::text(['name' => $this->faker->word]));
        $group->add(FormElement::text(['name' => $this->faker->word]));

        $this->assertCount(2, $group->elements());
    }

    /** @test - cannot add two elements with the same names */
    function cannot_add_two_elements_with_the_same_names() {
        $group = new FormGroup($name = $this->faker->word);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Element with name '" . ($elementName = $this->faker->word) . "' already in use");

        $group->add(FormElement::text(['name' => $elementName]));
        $group->add(FormElement::text(['name' => $elementName]));

        $this->assertCount(1, $group->elements());
    }

    /** @test - it adds two checkboxes with the same names */
    function it_adds_two_checkboxes_with_the_same_names() {
        $group = new FormGroup($name = $this->faker->word);

        $group->add(FormElement::checkbox(['name' => $elementName = $this->faker->word]));
        $group->add(FormElement::checkbox(['name' => $elementName]));

        $this->assertCount(2, $group->elements());
    }

    /** @test - it adds two checkboxes with the same names to the group within form */
    function it_adds_two_checkboxes_with_the_same_names_to_the_group_within_form() {
        $group = new FormGroup($name = $this->faker->word);
        $form = new Form();

        $form->addGroup($group);

        $group->add(FormElement::checkbox(['name' => $elementName = $this->faker->word]));
        $group->add(FormElement::checkbox(['name' => $elementName]));

        $this->assertCount(2, $group->elements());
        $this->assertEquals(2, $form->count());
    }

    /** @test - it counts elements in the group and in the form */
    function it_counts_elements_in_the_group_and_in_the_form() {
        $group = new FormGroup($name = $this->faker->word);
        $form = new Form();
        $element = FormElement::text(['name' => $elementName = $this->faker->word]);

        $group->add($element);
        $form->addGroup($group);

        $this->assertCount(1, $group->elements());
        $this->assertCount(1, $form->elements());
        $this->assertEquals(1, $group->count());
        $this->assertEquals(1, $form->count());
    }

    /** @test - it adds label to the group */
    function it_adds_label_to_the_group() {
        $group = new FormGroup($name = $this->faker->word);

        $this->assertNull($group->label());

        $group->setLabel($label = $this->faker->word);

        $this->assertEquals($label, $group->label());
    }

    /** @test - it adds label on group creation */
    function it_adds_label_on_group_creation() {
        $group = new FormGroup($name = $this->faker->word, $label = $this->faker->word);

        $this->assertEquals($label, $group->label());
    }

    /** @test - it render the group label */
    function it_render_the_group_label() {
        $group = new FormGroup($name = $this->faker->word, $label = $this->faker->word);

        $this->assertContains('<legend>'.$label.'</legend>', $group->render()->render());
    }

    /** @test - it adds custom parameters to fieldset on group creation */
    function it_adds_custom_parameters_to_fieldset_on_group_creation() {
        $group = new FormGroup($name = $this->faker->word, $label = $this->faker->word, [$paramKey = $this->faker->word => $param = $this->faker->word]);

        $this->assertEquals($param, $group->parameter($paramKey));
    }

    /** @test - it adds custom group parameters */
    function it_adds_custom_group_parameters() {
        $group = new FormGroup($name = $this->faker->word);

        $this->assertEmpty($group->parameters());

        $group->setParameter($param = $this->faker->word, $paramKey = $this->faker->word);

        $this->assertNotEmpty($group->parameters());

        $this->assertContains($paramKey.'="'.$param.'"', $group->render()->render());
    }

    /** @test - it removes group parameter */
    function it_removes_group_parameter() {
        $group = new FormGroup($name = $this->faker->word);

        $group->setParameter($param = $this->faker->word, $paramKey = $this->faker->word);
        $group->removeParameter($paramKey);

        $this->assertEmpty($group->parameters());
    }

    /** @test - it returns group element */
    function it_returns_group_element() {
        $group = new FormGroup($name = $this->faker->word);

        $element = FormElement::text(['name' => $elementName = $this->faker->word]);
        $group->add($element);

        $this->assertEquals($element, $group->element($elementName));
        $this->assertEquals($element, $group->getElement($elementName));
    }

    /** @test - it removes element by the name from the group */
    function it_removes_element_by_the_name_from_the_group() {
        $group = new FormGroup($name = $this->faker->word);

        $element = FormElement::text(['name' => $elementName = $this->faker->word]);
        $group->add($element);

        $this->assertCount(1, $group->elements());

        $group->removeElement($elementName);

        $this->assertCount(0, $group->elements());
    }

    /** @test - it removes element from the group */
    function it_removes_element_from_the_group() {
        $group = new FormGroup($name = $this->faker->word);

        $element = FormElement::text(['name' => $elementName = $this->faker->word]);
        $group->add($element);

        $this->assertCount(1, $group->elements());

        $group->remove($element);

        $this->assertCount(0, $group->elements());
    }

    /** @test - it removes element from the group within form */
    function it_removes_element_from_the_group_within_form() {
        $group = new FormGroup($name = $this->faker->word);
        $form = new Form();

        $group->setForm($form);

        $element = FormElement::text(['name' => $elementName = $this->faker->word]);
        $group->add($element);

        $this->assertCount(1, $group->elements());
        $this->assertEquals(1, $form->count());

        $group->removeElement($elementName);

        $this->assertCount(0, $group->elements());
        $this->assertEquals(0, $form->count());
    }
}
