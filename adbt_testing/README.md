# Automated Testing (Training)

`Automated Testing (Training)` module provides Drupal sample code. Taking this
as a base samples one can start having hands-on on the related topics.

At the moment this module contains examples covering implementation of:
- Unit Test
- Annotations & Filters
- Mocking Concept
- Kernel Test
- Functional Test
- Functional Javascript

**Course Link**:
https://learning.axelerant.com/courses/drupal-backend-module-development


## Installation & Permissions
***

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/documentation/install/modules-themes/modules-8

Install `drupal/code-dev` package before installing drush/drush.
```
ddev composer require --dev drupal/core-dev
```

If you already have drush installed,
them remove it and re-install after installing `drupal/code-dev`.
```
ddev composer remove drush/drush
ddev composer require --dev drupal/core-dev
ddev composer require drush/drush
```


No sepcific user permission required.


## Usage
***


### **- Unit Test**:

Run Unit test within DDEV shell:

```shell
ddev ssh
cd web
../vendor/bin/phpunit modules/custom/adbt_testing/tests/src/Unit/AdbtUnitBasicUtilityTest.php
```

> We have copied file web/core/phpunit.xml.dist to web/phpunit.xml, so that- we do not loose the settings if we upgrade the core.
> If you wish configure directly the web/core/phpunit.xml.dist and use it, then you have to add `-c core/phpunit.xml.dist` while running the test case. Example,

```shell
../vendor/bin/phpunit -c core/phpunit.xml.dist modules/custom/adbt_testing/tests/src/Unit/AdbtUnitBasicUtilityTest.php
```
__Note__:
Drupal site installation is not require to run PHPUnit tests.


### **- Kernel Test**:
Kernel test requires a database connection details at least, i.e., SIMPLETEST_DB environement variable set.

Run Kernel test within DDEV shell:

```shell
ddev ssh
cd web
../vendor/bin/phpunit modules/custom/adbt_testing/tests/src/Kernel/AdbtKernelUtilityConfigTest.php
```


### **- Functional Test**:
Functional test requires a base URL detail, i.e., SIMPLETEST_BASE_URL environement variable set.

Run Functional test within DDEV shell:

```shell
ddev ssh
cd web
../vendor/bin/phpunit modules/custom/adbt_testing/tests/src/Functional/AdbtFunctionalUserPriviledgeTest.php
```


### **- Functional Test**:

Run Functional test within DDEV shell:

```shell
ddev ssh
cd web
../vendor/bin/phpunit modules/custom/adbt_testing/tests/src/FunctionalJavascript/AdbtFunctionalJsAjaxTest.php
```

> You may opt to setup your development enviroment referring https://www.drupal.org/docs/automated-testing/phpunit-in-drupal/running-phpunit-javascript-tests to run these tests.
> To setup Macintosh-M1 machine we have opted Selenium Standalone (Installed using Homebrew & run it on port 4444).
```
brew install selenium-server-standalone
/opt/homebrew/opt/selenium-server/bin/selenium-server standalone --port 4444
```

> After runnning the selenium server on port 4444, you will find message on terminal
`Started Selenium Standalone 4.1.2 (revision 9a5a329c5a): http://192.168.29.49:4444`
> Use this IP value to configure `MINK_DRIVER_ARGS_WEBDRIVER` environment variable in phpunit.xml file.


> If we run FunctionalJavascript test without webdriver running it will skip the test.
```
OK, but incomplete, skipped, or risky tests!
Tests: 1, Assertions: 1, Skipped: 1.
```


### **- Annotations & Filters**:

Run a particular test case, using --filter attribute:
```
../vendor/bin/phpunit
modules/custom/adbt_testing/tests/src/FunctionalJavascript/AdbtFunctionalJsAjaxTest.php --filter testCreateScreenshotHomePage
```

Run all UNIT test cases of my module tagged to particular group, using --group attribute:
> Test methods must have @group annotation with given/specific value)
```
../vendor/bin/phpunit
modules/custom/adbt_testing/tests/src/Unit
--group add_numbers
```

Run test cases from different files covering getSiteName() method, using --covers attribute:
> Test methods must have @covers annotation with given/specific value)
```
../vendor/bin/phpunit
--covers 'Drupal\adbt_testing\Service\AdbtTestingConfigUtility::getSiteName'
```

Run all UNIT & KERNEL test cases, using --testsuite attribute:
```
../vendor/bin/phpunit
--testsuite unit,kernel
```

Use --verbose and/or --debug attributes to get more details about the running test cases.
```
../vendor/bin/phpunit
modules/custom/adbt_testing/tests/src/Unit/
--verbose --debug
```
