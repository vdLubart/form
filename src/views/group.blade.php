<div id="group-{{ $group->name() }}" @foreach($group->parameters() as $key=>$parameter) {{ $key }}="{{ $parameter }}"  @endforeach>
@if(!is_null($group->label()))
<fieldset>
    <legend>{{ $group->label() }}</legend>
@endif

@foreach($group->getElements() as $key=>$element)
    @include('lubart.form::element')
@endforeach

@if(!is_null($group->label()))
</fieldset>
@endif
</div>