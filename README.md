# RouteFinder

### WHY ?

I wanted to be able to use what I know best: PHP and Joomla to make a useful tool for the Joomla Developers Community
and allow more people to grasp the TRUE POWER of Web Services / Api in Joomla 4.x and the upcoming Joomla 5.x.

GOAL: Fetch and extract Joomla! 4.x Api Endpoints from source code then generate OpenAPI Schema 3.0.0 out of it.

### HOW ?

When using **stable mode**:

This composer script will run all the steps necessary for stable version of Joomla. Version if the form of 4-3-4 (with
dashes not dots). You can configure which version you want to find Web Services routes for with an environment variable
in the composer script next the line ``` @putenv APP_STABLE=4-3-4 ```

```shell
composer routefinder-all-stable
```

1. First step is to fetch the latest stable release from https://www.joomla.org website
2. Extract the contents in a temp directory
3. Statically analyse the code with nikic/php-parser
4. Extract Api urls, methods, models from code analysed without running it

SIDE NOTE:
RouteFinder should extract Web Services routes without the need to install Joomla. But if you want to run the SmokeTests
with Joomla Framework Http package and phpunit 10, you will need to install the Joomla this script just downloaded by
pointing your local webroot on your local web server to the folder called ``` extracted ```

When using **dev mode**:

Almost the same as stable mode but this time, this composer script will run all the steps necessary for git ref tag
version of Joomla. You can configure which version you want to find Web Services routes for with an environment variable
in the composer script next the line ``` @putenv APP_REF_TAG=5.0.0-beta1 ```

```shell
composer routefinder-all-dev
```

### STILL WORK IN PROGRESS

Extraction is done correctly but now, RouteFinder needs to:

- Parse the found routes
- Map path variables with correct regex validation for types (mainly integers but not for all routes)
- Reconstruct properly formatted routes
- Generation corresponding OpenAPI 3.0.0 Schema (which is already in almost complete state in Joomla Manual
  exactly [here](https://github.com/joomla/Manual/blob/feature/openapi/docs/using-core-functions/webservices/assets/webservices-openapi.yaml))
- My end goal with this project is to generate this kind of file in a completely automated way using PHP and Joomla then
  contribute back to Joomla Manual in order to ease the tedious work and pain raised by manually creating this file
  which is error-prone and less than ideal to manually unless the file is short.
