# CSRF Protection

CSRF (Cross-Site Request Forgery) is a type of attack that occurs when a malicious website, email, or program causes a user's web browser to perform an unwanted action on a trusted site for which the user is currently authenticated.

Monarch provides a simple CSRF protection mechanism to help prevent these types of attacks. This protection is enabled by default and works by adding a CSRF token to forms and verifying the token on form submission.

## Generating a CSRF Token

To protect your forms from CSRF attacks, you need to generate a CSRF token and include it in your form. Monarch provides a helper function to generate a CSRF token inside a hidden input field:

```php
<?= csrfInput() ?>
// Generates: <input type="hidden" name="csrf_token" value="...">
```

You can further increase the security of the page by locking the CSRF token to the current URI. This will use a second token that is unique to the current page as part of a hash to generate the CSRF token:

```php
<?= csrfInput(true) ?>
```

### Manually Generating a CSRF Token

If you need to generate a CSRF token manually, you can use the `csrf()` function:

```php
$response->withHeader(new Header('X-CSRF-Token', csrf()));
```

## Verifying a CSRF Token

When a form is submitted within a POST request, the `Security` middleware will automatically verify the CSRF token. If the token is invalid, the request will be rejected with a `403 Forbidden` status code and a message will be displayed.

If you need to manually verify a CSRF token, you can use the `veirfy` function of the CSRF class:

```php
use Monarch\HTTP\CSRF;

if (!CSRF::verify($token) {
    // Handle invalid CSRF token
}
```
