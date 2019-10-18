@if(isset($errors))
    <?php
    $errorBag = $errors->{$form->errorBag()};
    ?>
    @if($errorBag->any())
        <div class="lubart-form__alert lubart-form__alert-danger">
            <ul>
                @foreach($errorBag->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endif