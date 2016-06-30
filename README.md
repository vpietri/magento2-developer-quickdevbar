Developer Toolbar for Magento2
====================================

# About

Hope this debug toolbar can speed up Magento2 development module. Any feedback and idea to improve this toolbar will be appreciate so get in touch via the [issue tracker on GitHub](https://github.com/vpietri/magento2-developer-quickdevbar/issues). Feel free to fork and pull request.
Structure of this toolbar is extremly simple you just need to add a new block in the layout to get your tab running. 

# Features

## Panels

- Info : Information about controller, route, action and store. A dedicated tab output a phpinfo.
- Design : List handles called and display layout structure of nested blocks and containers
- Profile : View current observers, all events dispatched and collections, models loaded
- Queries :  Statistics about executed queries and detailed query listing with syntax highlighting of main SQL keywords
- Logs : Display log files with ability to reset these files
- Actions : Easily toggle template hints and inline translation and flush cache

## Screenshots

- Info tab
![](doc/images/qdb_screen_request.png)

- Queries Tab
![](doc/images/qdb_screen_queries.png)

- Profile Tab
![](doc/images/qdb_screen_dispatch.png)

# Installation

## Manual

Copy files under app/code/ADM/QuickDevBar folder.

## Composer

In the root directory

- Magento composer installer
```
composer require magento/magento-composer-installer
```

- Add the VCS repository: So that composer can find the module. Add the following lines in your composer.json

        "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vpietri/magento2-developer-quickdevbar"
        }],


- Install the module
```
composer require vpietri/adm-quickdevbar
```

## Cleaning

- Update Magento setup
```
php bin/magento setup:upgrade
```

- Update Magento data version
```
php bin/magento setup:db-data:upgrade
```

- Clear cache
```
php bin/magento cache:flush
```

## Setup

The toolbar is displayed by default if your web server is on your local development environment with the standard IPv4 address: 127.0.0.1. If not you can specify your IP on define an part of your http header in the config.   


# Changelog

0.1.6.1
* Fix compatibility bugs with 2.1

0.1.6
* UI improvement
* Add Block subtab
* Add icon from [iconsdb.com](http://www.iconsdb.com/)

0.1.5.2
* Fit to PHP coding standards

0.1.5.1
* Fix tab bug in backoffice

0.1.5
* Back office toolbar
* Reorganize tabs
* Add list of collection and model instanciated
* Add [Christian Bach's tablesorter plugin](https://github.com/christianbach/tablesorter)

0.1.4
* Fix bug on composer.json with registration.php
* Clean layout display

0.1.3
* Compatibility with Magento 2.0.0 Publication
* Add action tab (Template hints, Translate inline, Flush Cache Storage)
* Controller structure cleaning 

0.1.2
* Add sub-tab and reorganize existing tabs

0.1.1
* Javascript cleaning to meet coding standards
* Add [sunnywalker/filterTable](https://github.com/sunnywalker/jQuery.FilterTable)
* Fix bugs on the log screen
* Css improvements

0.0.1
*  module initialization 
