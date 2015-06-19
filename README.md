Developer Toolbar for Magento2
====================================

# About

This is an handmade toolbar by a developer for developers. As it is my first extension on Magento 2 I would appreciate any feedback and idea to improve this toolbar. 
Feel free to fork and pull request. Structure of this toolbar is extremly simple you just need to add a new block in the layout to get your tab running. 

# Requirements

- Magento Composer Installer: To copy the module contents under app/code/ folder.
In order to install it run the below command on the root directory:

        composer require magento/magento-composer-installer

- Add the VCS repository: So that composer can find the module. Add the following lines in your composer.json

        "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vpietri/magento2-developer-quickdevbar"
        }],


# Installation

- Add the module to composer:

        composer require vpietri/adm-quickdevbar

- Add the new entry in `app/etc/config.php`, under the 'modules' section:

        'ADM_QuickDevBar' => 1,

- Clear cache

# Coming soon

* Back office toolbar
* Layout improvement display
* Basic actions: Clear cache, Rebuit index, ...  

# Changelog

0.1.1
* Javascript cleaning to meet coding standards
* Add [sunnywalker/filterTable](https://github.com/sunnywalker/jQuery.FilterTable)
* Fix bugs on the log screen
* Css improvements

0.0.1
*  module initialization 
