# Validation

Monarch provides a validation framework that allows you to validate your data against a set of rules. It is a wrapper around the [Somnambulist Validation](https://github.com/somnambulist-tech/validation) library, which provides a flexible, [Laravel](https://laravel.com)-like validation system, with no dependencies.

!!! tip
    There are many more details in the [Somnambulist Validation documentation](https://github.com/somnambulist-tech/validation) about using the library directly and customizing it. This documentation will focus on the Monarch integration and basic usage.

!!! info

    The validation system is available via the `validation()` helper function. This function returns an instance of the `Validation` class. The `Validation` class is a singleton, so you can use the helper function to access the same instance throughout your application. This is the method these examples will use.

    If you prefer to use the `Validation` class directly, you can use the `Validation::instance()` method to get a singleton instance manually.

    ```php
    use Monarch\Validation;

    $validator = Validation::instance();
    Validation::instance()->make($data, $rules)->validate();
    Validation::instance()->errors();
    ```

## Basic Usage

There are two ways to use the validation system: either the `make()` or `validate` methods. The `make()` method creates a new instance of the validator, and then you'll manually call the `validate()` method.


```php
validation()->make($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
]);
validation()->validate();
```

The second way is to use the `validate()` method directly. This method will create a new instance of the validator and validate the data in one go.

```php
validation()->validate($_POST + $_FILES, [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
]);
```

## Validated, Valid, and Invalid Data

After validating, the `validation` object contains all of the results.

```php
$input = validation()->validate($_POST + $_FILES, [
    'title' => 'required',
    'body' => 'required',
    'published' => 'default:1|required|in:0,1',
    'something' => 'required|numeric'
]);

// Returns all of the data that was validated
$validation->getValidatedData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1' // notice this
//     'something' => '-invalid-'
// ]

$validation->getValidData();
// [
//     'title' => 'Lorem Ipsum',
//     'body' => 'Lorem ipsum dolor sit amet ...',
//     'published' => '1'
// ]

$validation->getInvalidData();
// [
//     'something' => '-invalid-'
// ]
```

## Error Messages

Error messages are collected in an `ErrorBag` instance that you can access via `errors()` on the validation instance.

```php
use Somnambulist\Components\Validation\Factory;

$validation = validation()->validate($inputs, $rules);

$errors = $validation->errors();
```

The error bag supports the following methods:

```php
// Get all errors
$errors->all();
// Get the first message of all existing keys
$errors->firstOfAll();
// Get the first message for a given key
$errors->first('key');
// Get all errors as an associative array
$errors->toArray();
// Count the errors
$errors->count();
// Check if the given key has an error
$errors->has('key');
```


## Custom Validation Rules

If you would like to add custom validation rules, you can do so by creating a new rule class as described in the [library documentation](https://github.com/somnambulist-tech/validation?tab=readme-ov-file#registeroverride-rules). Once you have created your rule you can add register it in the `config/app.php` file.

```php
return [
    ...
    'validationRules' => [
        'custom_rule' => \App\Rules\CustomRule::class,
    ],
];
```

Monarch will then ensure that the rule is registered with the underlying library and available for use in your validation rules.
