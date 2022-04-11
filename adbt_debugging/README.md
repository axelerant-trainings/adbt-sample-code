# Debugging & Troubleshoot (Training)

`Debugging & Troubleshoot (Training)` module provides Drupal sample code. Taking this
as a base samples one can start having hands-on on the related topics.

At the moment this module contains XDebug examples that covers:
- Debugging PHP Code
- Debugging Twig Template
- Debugging Javascript

**Course Link**:
https://learning.axelerant.com/courses/drupal-backend-module-development


## Installation & Permissions

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/documentation/install/modules-themes/modules-8

No sepcific user permission required.


## XDebug Setup

In DDEV setup check if xdebug is enabled
```
ddev xdebug status
```

In case disabled, enable xdebug
```
ddev xdebug status
```

### Visual Studio Code
Install extension - `PHP Intelephense`

File .vscode/launch.json shall has below configuration

```
  "configurations": [
    {
      "name": "Listen for Xdebug",
      "type": "php",
      "request": "launch",
      "port": 9000,
      "pathMappings": {
        "/var/www/html": "${workspaceRoot}"
      }
    }
  ]
```

### PHPStorm
No additional configuration required.

## Debug Twig Templates (XDebug)

### Visual Studio Code
Install & enable [Twig Xdebug](https://www.drupal.org/project/twig_xdebug) module.

```
composer require --dev drupal/twig_xdebug
drush en twig_xdebug
```
Now, place {{ breakpoint() }} expression in your twig template to debug.


### PHPStorm
Twig Xdebug module is not required, and hence you may uninstall it.
```
drush pmu twig_xdebug
```

Now, in PHPStorm navigate to
Preferences >> PHP >> Debug >> Templates >> Twig Debug

1. Add `Cache Path` value as:
/Users/my_user/my_project/web/sites/default/files/php/twig

2. In your web/sites/development.services.yml file use below parameter values
for twig.config
```
parameters:
  twig.config:
    debug: true
    auto_reload: true
```

3. In your web/sites/default/settings.php file add below lines of code to make
```
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
$settings['cache']['bins']['dynamic_page_cache'] = 'cache.backend.null';
```


## Debug Drush Commands (XDebug)

### Visual Studio Code
No additional configuration required.

### PHPStorm

In PHPStorm navigate to Preferences >> PHP >> Server
And update `Absolute path on server` against vendor directory as /var/www/html/vendor

Now, run drush commands within DDEV shell
```
ddev ssh
drush my_command
```

## Lando Setup (XDebug)

### Visual Studio Code
Copy file `.lando.local.yml` & `.lando.php.ini` from the reference directory of this module, and place it in your project parallel to the `web` directory.

Now, rebuild the lando:
```
lando rebuild
```

### PHPStorm

Copy file `.lando.local.yml` & `.lando.php.ini` from the reference directory of this module, and place it in your project parallel to the `web` directory.

Now, rebuild the lando:
```
lando rebuild
```
