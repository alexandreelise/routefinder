# RouteFinder

![visitor badge](https://visitor-badge.laobi.icu/badge?page_id=alexandreelise.routefinder&style=flat&format=true)
![GitHub followers](https://img.shields.io/github/followers/alexandreelise?style=flat)
![YouTube Channel Views](https://img.shields.io/youtube/channel/views/UCCya8rIL-PVHm8Mt4QPW-xw?style=flat&label=YouTube%20%40Api%20Adept%20vues)


<pre>

    __  __     ____         _____                              __                      __              
   / / / ___  / / ____     / ___/__  ______  ___  _____       / ____  ____  ____ ___  / ___  __________
  / /_/ / _ \/ / / __ \    \__ \/ / / / __ \/ _ \/ ___/  __  / / __ \/ __ \/ __ `__ \/ / _ \/ ___/ ___/
 / __  /  __/ / / /_/ /   ___/ / /_/ / /_/ /  __/ /     / /_/ / /_/ / /_/ / / / / / / /  __/ /  (__  ) 
/_/ /_/\___/_/_/\____/   /____/\__,_/ .___/\___/_/      \____/\____/\____/_/ /_/ /_/_/\___/_/  /____/  
                                   /_/                                                                 


</pre>

> ![GitHub Repo stars](https://img.shields.io/github/stars/alexandreelise/routefinder?style=flat) ![GitHub forks](https://img.shields.io/github/forks/alexandreelise/routefinder?style=flat) ![GitHub watchers](https://img.shields.io/github/watchers/alexandreelise/routefinder?style=flat)

### WHY ?

I wanted to be able to use what I know best: PHP and Joomla to make a useful tool for the Joomla Developers Community
and allow more people to grasp the TRUE POWER of Web Services / Api in Joomla 5.x and beyond.

GOAL: Fetch and extract Joomla! 5.x and beyond API Endpoints from source code then generate OpenAPI Schema 3.0.0 out of it.

### HOW ?

This project should work on Linux,Unix based OS like Ubuntu or macOS. Not tested on Windows.

First command to type in your terminal:
Clone this repo:

```shell

git clone https://github.com/alexandreelise/routefihder.git

```

Then, go to the directory

```shell

cd routefinder

```

Then install the dependencies

```shell

composer install

```

Then list composer script descriptions made for routefinder:

```shell

composer list

```


When using **stable mode**:

This composer script will run all the steps necessary for stable version of Joomla. Version if the form of 5-0-0 (with
dashes not dots). You can configure which version you want to find Web Services routes for with an environment variable
in the composer script next the line ``` @putenv APP_STABLE=5-0-0 ```

```shell

composer routefinder-all-stable

```

1. First step is to fetch the latest stable release from https://www.joomla.org website
2. Extract the contents in a temp directory
3. Statically analyse the code with nikic/php-parser
4. Extract Api urls, methods, models from code analysed without running it

SIDE NOTE:
RouteFinder should extract Web Services routes without the need to install Joomla. But if you want to run the
SmokeTests. For those tests to work, you will need to rename api-config.dist.ini to api-config.ini and provide your
download Joomla Api Token after using RouteFinder.

REMEMBER NOT TO LEAK YOUR API TOKENS IN YOUR REPO. You can use security related tools for that purpose. E.g.
GitGuardian, Snyk, etc...

with Joomla Framework Http package and phpunit 10, you will need to install the Joomla this script just downloaded by
pointing your local webroot on your local web server to the folder called ``` extracted ```

When using **dev mode**:

Almost the same as stable mode but this time, this composer script will run all the steps necessary for git ref tag
version of Joomla. You can configure which version you want to find Web Services routes for with an environment variable
in the composer script next the line ``` @putenv APP_REF_TAG=5.0.0-rc2 ```

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
