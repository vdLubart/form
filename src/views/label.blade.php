<label for="{{ $element->name() }}">
    {{ $element->label() }}
    @if($element->isObligatory())
        <span class="lubart-form__obligatoryMark">{{ $element->obligationMark() }}</span>
    @endif
</label>