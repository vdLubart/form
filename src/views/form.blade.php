@include('lubart.form::errors')

{!! Form::open([ 'url' => $form->action(), 'method'=>$form->method(), 'files'=>$form->files(), 'id'=>$form->id() ]) !!}

@foreach($form->groups() as $group)
    @include('lubart.form::group', ['group'=>$group])
@endforeach

@foreach($form->elements() as $key=>$element)
    @include('lubart.form::element')
@endforeach

{!! Form::close() !!}

@if(!is_null($form->jsFile()))
<script src='{{ $form->jsFile() }}' />
@endif

@if(!is_null($form->js()))
<script>
    {!! $form->js() !!}
</script>
@endif