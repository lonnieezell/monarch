# Debugging Tools

Monarch provides a few tools to help you debug your application. As web developers the browser is our playground. The debugging tools take this to heart. Many of them are designed to be used in the browser's console.

## Console Logging

The `debug()` helper function provides a wrapper around many of Javascript's `console` logging functions. The methods are used within the PHP files, but the logs are displayed in the browser's console. The display has custom styling to make it instantly recognizable as a Monarch log.

```php
debug()->error('Your message');
debug()->info('Your message');
debug()->warn('Your message');
debug()->debug('Your message');
debug()->log('Your message');
```

These are the basic logging methods and will output the appropriate log level in the console. The first parameter is the item to display. This can be a string, array, object, or any other type of data. You can also use the second parameter to specify a label for the log. The label will be displayed in the console before the item, surrounded by square brackets. If you are logging an array or object, the label will be displayed on the line above the item, using the "log" styling.

```php
debug()->log('Your message', 'Label');
// [Label] Your message
```

### Logging Tabular Data

If you have an array of arrays, you can log it as a table. This is useful for displaying data in a more readable format.

```php
$users = [
    ['name' => 'John', 'age' => 26],
    ['name' => 'Jane', 'age' => 28],
];

debug()->table($users);
```

### Grouping Logs

If you have many logs to display you may find that the console becomes cluttered. You can group logs together to keep things organized.  Each group will be collapsible in the console. You specify the group by calling the `group` method, and then call `groupEnd` to close the group.

```php
debug()->group('Group Label');
debug()->log('Your message');
debug()->groupEnd();
```
### Configuring Logging

By default, the log items are echoed out at the location that you call the `debug()` function. You can change this behavior by setting the `capture` option to `true` in the `debug` config file. This will display all of the logs at the end of the page, just prior to the closing `</body>` tag. The advantage here is that the script tags that do the logging do not clutter up the HTML source. This does come at a cost of not seeing what line in the generated HTML the log was called from.