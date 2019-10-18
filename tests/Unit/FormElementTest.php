<?php
/**
 * @author Viacheslav Dymarchuk
 */

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;
use Lubart\Form\Form;

class FormElementTest extends TestCase {

    use WithFaker;

    /** @test */
    function it_builds_text_form_element_with_default_data(){
        $element = FormElement::text();

        $this->assertEquals('submit', $element->name());
        $this->assertEquals('', $element->label());
        $this->assertEquals('text', $element->type());
        $this->assertEquals('', $element->value());
        $this->assertEmpty($element->parameters());
        $this->assertFalse($element->isObligatory());
        $this->assertNull($element->form());
        $this->assertFalse($element->isChecked());
        $this->assertNull($element->options());
    }

    /** @test */
    function it_builds_text_element_with_parameters(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word,
            'placeholder' => $placeholder = $this->faker->sentence,
        ]);

        $this->assertEquals($name, $element->name());
        $this->assertEquals($label, $element->label());
        $this->assertEquals('text', $element->type());
        $this->assertEquals($value, $element->value());
        $this->assertCount(1, $element->parameters());
        $this->assertEquals($placeholder, $element->parameters()['placeholder']);
    }

    /** @test */
    function it_renders_text_element(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="text" value="'.$value.'">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_textarea_element(){
        $element = FormElement::textarea([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<textarea name="'.$name.'" cols="50" rows="10">'.$value.'</textarea>', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_password_element(){
        $element = FormElement::password([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="password" value="">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_email_element(){
        $element = FormElement::email([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->email
        ]);

        $this->assertContains('<input name="'.$name.'" type="email" value="'.$value.'">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_file_element(){
        $element = FormElement::file([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="file">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_checkbox_element(){
        $element = FormElement::checkbox([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="checkbox" value="'.$value.'">', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_checked_checkbox_element(){
        $element = FormElement::checkbox([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->check();
        $this->assertContains('<input checked="checked" name="'.$name.'" type="checkbox" value="'.$value.'">', $element->render()->render());

        $element->uncheck();
        $this->assertContains('<input name="'.$name.'" type="checkbox" value="'.$value.'">', $element->render()->render());
    }

    /** @test */
    function it_renders_radio_element(){
        $element = FormElement::radio([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="radio" value="'.$value.'">', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_number_element(){
        $element = FormElement::number([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->randomNumber()
        ]);

        $this->assertContains('<input name="'.$name.'" type="number" value="'.$value.'">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_select_element(){
        $element = FormElement::select([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'options' => $words = $this->faker->words,
            'value' => $value = 1
        ]);

        $select = '<select name="'.$name.'">';
        foreach($words as $key=>$word){
            $select .= '<option value="'.$key. '"'.($key == $value ? ' selected="selected"' : '').'>'.$word.'</option>';
        }
        $select .= '</select>';

        $this->assertContains($select, $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());

        $element->addOption($key = $this->faker->word, $word = $this->faker->word);
        $this->assertContains($word, $element->options());
        $this->assertContains('<option value="'.$key. '">'.$word.'</option>', $element->render()->render());
    }

    /** @test */
    function it_renders_hidden_element(){
        $element = FormElement::hidden([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" type="hidden" value="'.$value.'">', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
    }

    /** @test */
    function it_renders_button_element(){
        $element = FormElement::button([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<button name="'.$name.'" class="lubart-form__button" type="button">'.$value.'</button>', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
    }

    /** @test */
    function it_renders_submit_element(){
        $element = FormElement::submit([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertContains('<input name="'.$name.'" class="lubart-form__button" type="submit" value="'.$value.'">', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
    }

    /** @test */
    function it_renders_html_element(){
        $element = FormElement::html([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->randomHtml()
        ]);

        $this->assertContains($value, $element->render()->render());
        $this->assertNotContains('<input name="'.$name.'"', $element->render()->render());
        $this->assertNotContains('<label for="'.$name.'">'.$label.'</label>', $element->render()->render());
    }

    /** @test */
    function it_renders_date_element(){
        $element = FormElement::date([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->date()
        ]);

        $this->assertContains('<input name="'.$name.'" type="date" value="'.$value.'">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_renders_time_element(){
        $element = FormElement::date([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->time()
        ]);

        $this->assertContains('<input name="'.$name.'" type="date" value="'.$value.'">', $element->render()->render());
        $this->assertContains('<label for="'.$name.'">', $element->render()->render());
        $this->assertContains($label, $element->render()->render());
    }

    /** @test */
    function it_sets_additional_option_to_element(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->setParameter($placeholder = $this->faker->word, 'data-placeholder');

        $this->assertEquals($placeholder, $element->parameter('data-placeholder'));
    }

    /** @test */
    function cannot_tick_text_element(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertFalse($element->isChecked());

        $element->check();

        $this->assertFalse($element->isChecked());
    }

    /** @test */
    function cannot_set_options_to_text_element(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertNull($element->options());

        $element->addOption($this->faker->word, $this->faker->word);

        $this->assertNull($element->options());
    }

    /** @test */
    function it_removes_existing_parameter(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->setParameter($paramValue = $this->faker->word, $paramKey = $this->faker->word);

        $this->assertEquals($paramValue, $element->parameter($paramKey));

        $element->removeParameter($paramKey);

        $this->assertNull($element->parameter($paramKey));
    }

    /** @test */
    function cannot_remove_name_parameter(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->removeParameter('name');

        $this->assertEquals($name, $element->parameter('name'));
    }

    /** @test */
    function cannot_remove_value_parameter(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->removeParameter('value');

        $this->assertEquals($value, $element->parameter('value'));
    }

    /** @test */
    function is_marks_element_as_obligatory(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $this->assertFalse($element->isObligatory());

        $element->obligatory();

        $this->assertTrue($element->isObligatory());
    }

    /** @test */
    function is_marks_element_as_not_obligatory(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->obligatory();

        $element->notObligatory();

        $this->assertFalse($element->isObligatory());
    }

    /** @test */
    function it_adds_element_to_the_group(){
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);
        $group = new FormGroup($this->faker->word);

        $this->assertNull($element->group());

        $element->setGroup($group);

        $this->assertEquals($group, $element->group());
    }

    /** @test - it renders element with obligation mark */
    function it_renders_element_with_obligation_mark() {
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->obligatory();

        $this->assertContains('<span class="lubart-form__obligatoryMark">*</span>', $element->render()->render());
    }

    /** @test - it sets obligation mark to element */
    function it_sets_obligation_mark_to_element() {
        $element = FormElement::text([
            'name' => $name = $this->faker->word,
            'label' => $label = $this->faker->sentence,
            'value' => $value = $this->faker->word
        ]);

        $element->obligatory()
            ->setObligationMark("!");

        $form = new Form();
        $form->add($element);
        $form->add($secondElement = FormElement::text(['name'=>'text', 'label'=>'Label'])->obligatory());

        $this->assertContains('<span class="lubart-form__obligatoryMark">!</span>', $element->render()->render());
        $this->assertContains('<span class="lubart-form__obligatoryMark">*</span>', $secondElement->render()->render());
    }
}
