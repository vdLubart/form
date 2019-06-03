@if(!empty($element))
    <div id="element-{{ $element->name() }}" class="margin10-bottom">
    @if($element->type() == 'select')
        @if(!empty($element->label()))
            {!! Form::label($element->name(), $element->label())!!}<br/>
        @endif
        {!! Form::{$element->type()}(
            $element->name(),
            $element->options(),
            $element->value(),
            $element->parameters()
            );
        !!}
    @elseif(in_array($element->type(), ['checkbox', 'radio']))
        <label>
            {!! Form::{$element->type()}(
                $element->name(),
                $element->value(),
                $element->check(),
                $element->parameters()
                );
            !!}
            {!! $element->label() !!}
        </label>
    @elseif(in_array($element->type(), ['submit', 'button']))
        {!! Form::{$element->type()}(
            $element->value(),
            $element->parameters()+['name'=>$element->name(), 'class'=>'btn btn-primary']
            );
        !!}
    @elseif($element->type() == 'html')
        {!! $element->value() !!}
    @else
        @if($element->type() != "hidden" and !empty($element->label()))
            {!! Form::label($element->name(), $element->label())!!}<br/>
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