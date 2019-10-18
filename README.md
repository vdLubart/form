# Laravel Form Builder

Package helps to build object-oriented structure for web form. It gives more control under
the form data from the Controller side and makes View blade files more cleaner and readable.
Within package and be created different types of web form elements, developer has full control
over each element and can specify label, name, value, class and any other custom parameters.
Additionally form elements can be combined in the group with full control over this block. 

## Installation

This package is private and stored only on the internal server. To
reach this server you should add to your `composer.json` following block:

```
"repositories": [{
  "type": "composer",
  "url": "https://packages.home"
}]
```

To install package you can run following command from the terminal:

```bash
composer require lubart/form
```

Composer will ask login and password to authorize on the https://packages.home server.

If you are using `Laravel v5.5` or higher, package will be automatically recognized and no
action is needed, for `Laravel versions v5.1 - v5.4` package should be additionally 
registered in the `providers` section in the `config/app.php` file:

```php
...
'provider' => [
    ...
    \Lubart\Form\FormServiceProvider::class,
    ...
]
...
```

## Usage

After installation new form can be created in any class as follow:

```php
// IndexController.php

use Lubart\Form\Form;
use Lubart\Form\FormElement;
use Lubart\Form\FormGroup;

...

public functoin form(){
    $form = new Form();
    
    $form->add(FormElement::text(['name'=>'login', 'label'=>'Login']));
    
    // or alternatively
    $passwordElement = FormElement::password(['name'=>'password']);
    $passwordElement->setLabel('Password')
        ->obligatory();
    
    $form->add($passwordElement);
    
    return view('form')->with(['form'=>$form]); 
}

```

and then in the view file:

```php

{!! $form->render() !!}

```

### View Customization

Package generates form elements based on `laravelcollective/html` package, but view files can
be published from the package and customized to use any other web form template or clean HTML.
To publish package sources run from the terminal;

```bash
php artisan vendor:publish --tag=lubart-form-view
```

This will publish all view files to the `/resources/views/lubart-form` directory.
To use custom set of the view files, change form view environment:

```php
$form = new Form();

$form->setView('partial.form');
```
In that example main form blade file should be found at `resources/views/partial/form.blade.php`
 
### Styles publishing

Package styles can be published from the package as follow:

```php
php artisan vendor:publish --tag=lubart-form-style
```
This will publish compiled `lubart-form_styles.css` CSS file to the `/public/css` directory
and SASS project to the `/resources/sass/lubart-form` directory.

  