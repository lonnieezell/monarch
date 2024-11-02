# Components

As web development has matured, our applications have become more complex. We have moved from simple HTML pages to dynamic web applications that use JavaScript, CSS, and HTML to create rich user experiences. This complexity has led to the development of a new way to organize our code: components.

Components are self-contained pieces of code that can be reused across our application. They can be as simple as a button or as complex as a form. Components are a way to encapsulate the logic and presentation of a piece of the user interface. They can be used to create reusable elements that can be shared across different parts of your application.

There are two types of components: simple components and controlled components. Simple components are often stateless and are used to render UI elements that do not have any logic requirements. They are often used to help with styling the components using CSS frameworks like [TailwindCSS](https://tailwindcss.com/). Controlled components work much like controlled routes, where the component is responsible for managing its own state and rendering the UI based on that state.

## Creating Components

All components should be located within the `app/Components` directory. This directory is auto-loaded by the framework, so you can reference components by their class name without needing to import them. You can add additional directories to search for components in by adding it to the `componentPaths` array in the `config/app.php` file.

```php
return [
    'componentPaths' => [
        'x' => 'app/Components',
        'x' => 'app/CustomComponents',
    ],
];
```

Each component's tag in the HTML will have a prefix of `x-` to differentiate it from standard HTML tags. This is a convention that Monarch uses to make it clear that a tag is a custom component. You can change the prefix by updating the key of the `componentPaths` array in the `config/app.php` file.

```php
return [
    'componentPaths' => [
        'my' => 'app/Components',
    ],
];
```

## Simple Components

Simple components consist of a single PHP file that contains the HTML for the component. The file should be located in the `app/Components` directory and have a name that matches the desired tag name. Note that all custom tags must contain a hyphen (`-`) to be valid HTML, and Monarch standardizes this by prefixing all custom tags with `x-`.

For example, to create a simple component that renders a button, you would create a file named `app/Components/button.php` with content similar to this:

```html
<button type="button">
    <x-slot></x-slot>
</button>
```

Once this file is created, you can use the component in your views like this:

```html
<x-button>Click me</x-button>
```

The `slot` tag is a placeholder that will be replaced with the content inside the component tag.

#### Nested Component Folders

You can create nested folders within the `app/Components` directory to organize your components. For example, if you wanted to create a `form` component, you could create a folder named `form` and place the component file inside it. The name of the component would replace the directory separator with a period (.). So `form/fieldset` would be named `x-form.fieldset`. The component would then be used like this:

```html
<x-form.fieldset></x-form.fieldset>
```

### Attributes

You can pass attributes to a component by adding them to the component tag. The attributes will be available in the component as an object called `$attributes`. If echoed out directly, it will be a string of HTML attributes that can be added to the component tag.

```php
// app/components/button.php
<button type="button" class="btn btn-primary" <?= $attributes ?>>
    <x-slot></x-slot>
</button>
```

You can then use the component like this:

```html
<x-button id="submit" name="submit" value="Submit">Click me</x-button>
```

Attributes can also be accessed individually by using the `$attributes->get()` method.

```php
// app/components/button.php
<button type="button" class="btn btn-primary" id="<?= $attributes->get('id') ?>" <?= $attributes->except('id', 'class') ?>>
    <x-slot></x-slot>
</button>
```

You can also restrict the attributes that are output by using the `only` method.

```php
// app/components/button.php
<button type="button" class="btn btn-primary" <?= $attributes->only('id', 'name') ?>>
    <x-slot></x-slot>
</button>
```

### Default / Merged Attributes

Sometimes you may want to provide default attributes for a component that can be overridden by the user. You can do this by merging the default attributes with the user-provided attributes.

```php
// app/components/button.php
<button type="button" <?= $attributes->merge(['class' => 'btn btn-primary']) ?>>
    <x-slot></x-slot>
</button>
```

You can then use the component like this:

```html
<x-button class="btn-lg">Click me</x-button>
```

The resulting HTML will be:

```html
<button type="button" class="btn btn-primary btn-lg">Click me</button>
```

### Dynamic Attributes

Since the component file is just a PHP file, you can use PHP to generate dynamic attributes.

```php
// app/components/button.php
<?php
    if ($attributes->has('disabled')) {
        $class = 'btn btn-primary btn-disabled';
    } else {
        $class = 'btn btn-primary';
    }
?>

<button type="button" <?= $attributes->merge(['class' => $class]) ?>>
    <x-slot></x-slot>
</button>
```

### Slots

As we've seen, slots are a way to pass content into a component. They are placeholders that can be replaced with content when the component is used. Slots are defined using the `x-slot` tag in the component file and are replaced with the content inside the component tag.

```html
<!-- app/components/alert.php -->
<div class="alert alert-<?= $type ?>">
    <x-slot></x-slot>
</div>
```

You can then use the component like this:

```html
<x-alert type="success">
    <p>Success! Your changes have been saved.</p>
</x-alert>
```

#### Default Slot Content

You can provide default content for a slot by using the `x-slot` tag with a default value.

```html
<!-- app/components/alert.php -->
<div class="alert alert-<?= $type ?>">
    <x-slot>
        <p>Default content goes here.</p>
    </x-slot>
</div>
```

You can then use the component like this:

```html
<x-alert type="success"></x-alert>
```

The default content will be used if no content is provided.

#### Named Slots

You can define multiple slots in a component by using the `name` attribute on the `x-slot` tag.

```html
<!-- app/components/card.php -->
<div class="card">
    <div class="card-header">
        <x-slot name="header"></x-slot>
    </div>
    <div class="card-body">
        <x-slot name="body"></x-slot>
    </div>
</div>
```

You can then use the component like this:

```html
<x-card>
    <x-slot name="header">
        <h2>Card Title</h2>
    </x-slot>
    <x-slot name="body">
        <p>Card content goes here.</p>
    </x-slot>
</x-card>
```

## Controlled Components

Controlled components are used when you need more logic, or access to more of the framework, than a simple component provides. They are similar to controllers in that they are responsible for managing their own state and rendering the UI based on that state.

### Creating a Controlled Component

Controlled components are PHP classes that extend the `Component` class. They should be located in the `app/components` directory and have a name that matches the desired tag name, and have the `.control.php` extension. You will typically have a corresponding view file with the same name as the component, though it is not required.

For example, to create a controlled component that renders a form, you would create a file named `app/components/form.control.php` with content similar to this:

```php
<?php

use Monarch\Components\Component;

return new class() extends Component
{
    public $name;

    public function render(): string
    {
        return $this->view('form', ['name' => $this->name]);
    }
}
```

You are required to implement the `render()` method which must return a string. Other than that, you are free to add any additional methods or properties to the class as needed.

The class provides a `view()` method that returns a simple component view like was described earlier in this document. It automatically makes the `$attributes` object available to the view, and handles parsing any slots. In this case, the `form` view file would be located at `app/Components/form.php`.

### Using a Controlled Component

Controlled Components are called exactly like simple components, but the control file's `render` method is called instead of the component file's content being returned.

```html
<x-form name="contact">
    <x-slot></x-slot>
</x-form>
```

Within the component's control file, you can access the attributes and slots using the `$this->attributes` instance.

```php
<?php

use Monarch\Components\Component;

return new class() extends Component
{
    public function render(): string
    {
        return $this->view('form', ['name' => $this->attributes->get('name')]);
    }
}
```
