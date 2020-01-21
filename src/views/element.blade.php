@if(!empty($element))
    <div id="element-{{ $element->name() }}" class="lubart-form__element">
    @if($element->type() == 'select')
            @if(!empty($element->label()))
                @include('lubart.form::label', ['element'=>$element])<br>
            @endif
        {!! Form::{$element->type()}(
            $element->name(),
            $element->options(),
            $element->value(),
            $element->parameters()
            );
        !!}
    @elseif(in_array($element->type(), ['checkbox', 'radio']) and empty($element->options()))
        <label>
            {!! Form::{$element->type()}(
                $element->name(),
                $element->value(),
                $element->isChecked(),
                $element->parameters()
                );
            !!}
            {!! $element->label() !!}
        </label>
    @elseif(in_array($element->type(), ['checkbox', 'radio']) and !empty($element->options()))
        @if(!empty($element->label()))
            @include('lubart.form::label', ['element'=>$element])<br>
        @endif
        @foreach($element->options() as $value=>$option)
            {!! Form::{$element->type()}(
                $element->name(),
                $value,
                is_array($element->value()) ? in_array($value, $element->value()) : $value == $element->value(),
                $element->parameters()
                );
            !!}
            {!! $option !!}<br>
        @endforeach
    @elseif(in_array($element->type(), ['submit', 'button']))
        {!! Form::{$element->type()}(
            $element->value(),
            $element->parameters()+['name'=>$element->name(), 'class'=>'lubart-form__button']
            );
        !!}
    @elseif($element->type() == 'html')
        {!! $element->value() !!}
    @else
        @if($element->type() != "hidden" and !empty($element->label()))
            @include('lubart.form::label', ['element'=>$element])<br>
        @endif
        {!! Form::{$element->type()}(
            $element->name(),
            $element->value(),
            $element->parameters()
            );
        !!}
    @endif
    </div>
@endif