# Email

Monarch provides a simple way to send emails using the `Monarch\Email` class. This class is a wrapper around the [Laminas Mail](https://docs.laminas.dev/laminas-mail/) library, which provides a simple way to send emails using PHP. It provides transports for sending emails using SMTP, sendmail, [Resend](https://resend.com/), and more.

## Sending Emails

To send an email, you can use the `Monarch\Mail` class.

```php
use Monarch\Mail\Mail;

Mail::setView('template-name')
    ->setSubject('Welcome to Monarch')
    ->addTo('john.doe@example.com', 'John Doe')
    ->setData([
        'name' => 'John Doe',
        'url' => 'https://example.com',
    ])
    ->send();
```

The `setView` method is used to set the email template that you want to use. The views are expected to be within the `app/mail` directory. The `addTo` method is used to set the recipient's email address and name. The `setData` method is used to pass data to the email template. The `send` method is used to send the email.

The `setData` method is used to pass data to the email template. This data can be used in the email template to customize the content of the email.


Once you have set the view, subject, recipients, and data, you can send the email using the `send` method. This will send the email using the default transport and the default sender.

The `setView` method must be called first before calling any other methods. This is because the `setView` method sets returns a singleton instance of the Mail class, and create the underlying Laminas Mail Message object. You then have access to the underlying Laminas Mail Message object to set the subject, recipients, etc, and can access any of the methods provided by the Laminas Mail Message object.

## Templates

Monarch provides a set of components to help you build emails that will work across all email clients. These components are designed to be used with the `Monarch\Email` class.

Unlike the standard components that you might create, all email components are prefixed with `m-` instead of `x-`. This is to avoid conflicts with the standard components.

A simple email might look like this:

```html
<x-slot name="title">
    <m-title>Welcome to Monarch</m-title>
</x-slot>

<x-slot name="title">
    <m-heading as="h1">Welcome to Monarch</m-heading>
</x-slot>

<x-slot name="body">
    <m-text style="margin-bottom: 2em;">
        Thanks for signing up! We are excited to have you on board.
    </m-text>
    <m-button href="<?= $url ?>">Sign In</m-button>
</x-slot>
```

## Layouts

By default, your email will be wrapped in a layout specified at `app/mail/+layout.php`. This layout will contain the basic structure of the email, such as the doctype, head, and body tags. You can customize the layout to include any additional styles or scripts that you need. This can also use any of the components to build a basic structure and style.

```html
<!DOCTYPE html>
<m-html>
    <m-head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <x-slot name="title">
            <m-title>Reset Password</m-title>
        </x-slot>
    </m-head>
    <m-body style="background-color: #eaeaea;">
        <m-container style="background-color: #ffffff; border: 1px solid #c8c8c8; padding: 1rem 2rem;">
            <m-header style="">
                <x-slot name="header"></x-slot>
            </m-header>
            <x-slot name="body" style="padding-top: 2rem;"></x-slot>
        </m-container>
        <x-slot></x-slot>
    </m-body>
</m-html>
```

If you want to use a different layout for a specific email, you can use the `setLayout` method. The file must be located in the `app/mail` directory.

```php
Mail::setView('template-name')
    ->setLayout('+custom-layout')
    ->setSubject('Welcome to Monarch')
    ->addTo('john.doe@example.com', 'John Doe')
    ->send();
```

## Available Components

### `m-html`

The root element for the email. This should contain the `m-body` component. The default attributes are:

- dir: ltr
- lang: en

```html
<m-html>
    <m-body>
        <!-- Email content -->
    </m-body>
</m-html>
```

### `m-body`

The body of the email. This should contain all of the email content. The body tag has no default styling. You can always add your own styles to the body tag.

```html
<m-body style="padding: 2rem 4rem;">
    <!-- Email content -->
</m-body>
```

### `m-container`

A container for grouping elements together, constraining the width of the content, and centering the content in the window. The default attributes are:

- max-width: 37.5em
- margin: 0 auto

The container element is a table with a width of 100%. Any styles passed in apply to the table elemtent itself, not the row or cell elements.

```html
<m-container>
    <!-- Email content -->
</m-container>
```

### `m-row`

A row for grouping columns together. This should be used in conjunction with `m-column` tags. The default attributes are:

- border-collapse: collapse

The row compoment is a table and a single row with a width of 100%. Any styles passed in apply to the table row element itself, not the cell elements.

```html
<m-row>
    <!-- Email content -->
</m-row>
```

### `m-column`

A column for grouping content together. This should be used in conjunction with `m-row` tags. The table cell used in this component does not have any default styling.

```html
<m-row>
    <m-column>
        <!-- Email content -->
    </m-column>
</m-row>
```

### `m-heading`

A heading element for the email. You can define the heading level using the `as` attribute.

```html
<m-heading as="h1">Welcome to Monarch</m-heading>
```

### `m-text`

A text element for the email. This component is a paragraph element with some basic default styling:

- font-size: 14px
- line-height: 24px
- margin: 16px 0

```html
<m-text>
    Thanks for signing up! We are excited to have you on board.
</m-text>
```

### `m-link`

A link element for the email. This component is an anchor element with some basic default styling:

- color: #3490dc;
- text-decoration: none
- target: _blank

```html
<m-link href="https://example.com">Click here to learn more</m-link>
```

### `m-button`

A button element for the email. This component is an anchor element with some basic default styling:

- background-color: #3490dc;
- color: #ffffff;
- display: inline-block;
- line-height: 1.2;
- max-width: 100%;
- margin-left: auto;
- margin-right: auto;

```html
<m-button href="https://example.com">
    Click here to learn more
</m-button>
```

When passing styles to the `m-button` component, you can use the `style` attribute to add inline styles to the button element. The padding values must be in a single `padding` attribute.

```html
<m-button href="https://example.com" style="padding: 1em 2em;">
    Click here to learn more
</m-button>
```
