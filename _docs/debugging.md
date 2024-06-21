# Debugging

Monarch integrates [Tracy](https://tracy.nette.org/en/) to provide a powerful debugging experience. This includes a debug bar, error logging, an error screen, a stopwatch, IDE integration, and more. And it's fully expandable with [plugins](https://componette.org/search/tracy).

## Enabling Error Logging

Tracy is always enabled, as it handles error logging and error screens.

## Enabling the Debug Bar

While Tracy is always enabled, the debug bar is only enabled when the `DEBUG` environment variable is set to `1`. This is done in the `.env` file.

```env
DEBUG=1
```

## Configuration

Tracy can be configured in the `config/tracy.php` file. This file is automatically loaded by Tracy. See Tracy's [configuration documentation](https://tracy.nette.org/en/configuring) for more information.

## Known Issues

Tracy will not show up when clicking on HTMX-boosted links. I will continue to look into this. In the meantime, you can manually refresh the page to see the debug bar, or design your application to not rely on boosted links for debugging purposes.
