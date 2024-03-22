# Myth:work

Myth:work is an exploration of how simple and flexible a small PHP framework can be.

Its goal is to provide the minimum amount of tools and structure to help you create a web application,
while providing a structure that is easy to understand and maintain.

## Folder Structure

The following folders are used within Myth:work:

```
app/
config/
myth/
public/
resources/
routes/
tests/
```

### app/

The `app/` folder is where you will place all of your application-specific code that are not a route file or associated control file.
This is typically where you will place your models, libraries, and other classes.

### config/

Contains all of the configuration files for your application.

### myth/

This is the core of the Myth:work framework. It contains all of the code that makes Myth:work work.

### public/

This is the web root of your application. It contains the `index.php` file that is the entry point for all requests.

### resources/

This is where you can place all of your assets and language files.

### routes/

This is where you will define all of the route files, templates, and controller logic.

### tests/

This holds all of your application's tests.
