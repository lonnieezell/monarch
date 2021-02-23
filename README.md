# myth:work

_myth:work_ is an experimental playground (under construction) to see if it's possible to bring back much of the 
simplicity of "old-school" PHP/HTML/JS programming while still retaining enough of the modern PHP features to 
make it robust and comfortable. Unlike typical PHP frameworks, _myth:work_ eschews routing, heavy/complex sets 
of libraries, and quite frankly many of the tools that you're used to having. Instead it focuses on simplicity 
at every step, asking what the minimum it can provide and still be usable.

Inspired by [The Stackless Way](https://tutorials.yax.com/articles/the-yax-way/index.html) article, this is something
that I've attempted to do a few times in the past, but never gotten very far. Maybe I won't this time, either, but 
I like the direction this is heading so far.

## Basic Concepts

Every page that a user can visit on your website corresponds to a physical page on the server. This removes the need
for much of the boilerplate and routing that a framework needs and makes it simple to know exactly what file to 
look at to see what's going wrong. 

If it's a static HTML page, or even a page that contains a lot of functionality via Javascript then all you have to do 
is display the HTML. Because HTML doesn't provide a way to DRY your code, you can use the simple templating system to 
organize your views and fragments. View fragments are stored in `app/views`.

If you need to handle some business logic in the background, you're in luck. The system will automatically detect if
a controller exists for the current page, by looking for it in `app/controllers` where the structure maps exactly
with the script that's currently running. If the script running is `public/foo/bar.php`, it will load a controller
file found at `app/controllers/foo/bar.php`; Unlike traditional PHP frameworks, what the controller looks like is
completely up to you. If you want to use a class, go for it. If you prefer a more linear or functional approach 
then you're free to use those styles also. Like the old school days, though, you should make the data available 
to the main page. Controllers are always ran first so that data can be available in the view as needed. 

If you need to implement an API, then you could use the main page to call your custom class, grab some data, 
and then format it into a JSON response before returning it to the client. I'm still exploring this area. 

As for an custom PHP code you need, it's all built on [Composer](https://getcomposer.org), so autoloading
works out of the box. Edit `composer.json` to add a custom namespace or pull in any third-party components
you might need. 

A super simple database solution will be provided that will be mostly a convenience wrapper around PHPs driver.

## Configuration

Config files are simple arrays that contain the data you need to access. The values can always be accessed through 
the `config()` function that is always available. It takes a single parameters: a string that starts with the 
file name, then each key in an array, separated by periods. 

```
// Given the following array in config/email.php
'from' = [
    'name' => 'Foo Bar',
    'email' => 'foo@example.com
];

// Grab the name by: 
$name = config('email.name');
```

Any values stored within a `.env` file at the project root will be loaded into the environment during script startup.
This allows you to use the `env()` function to retrieve the value from the environment. The first argument is the
name of the key to locate, and the second being a default value that will be used when no value is found.

```
'emailDriver' => env('EMAIL_DRIVER', 'smtp'), 
```


