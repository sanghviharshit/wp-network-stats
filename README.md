# WP Network Stats

View/Export useful network information (e.g. #sites/user, #sites/theme, #sites/plugin, privacy settings, etc) of all the sites in a WordPress multisite network.

## Contents

The WordPress Plugin Boilerplate includes the following files:

* `.gitignore`. Used to exclude certain files from the repository.
* `CHANGELOG.md`. The list of changes to the core project.
* `README.md`. The file that you’re currently reading.
* A `wp-network-stats` subdirectory that contains the source code - a fully executable WordPress plugin.

## Features

* The Plugin is written on top of the [WordPress Plugin Bolierplate](https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate).
* It is based on the [Plugin API](http://codex.wordpress.org/Plugin_API), [Coding Standards](http://codex.wordpress.org/WordPress_Coding_Standards), and [Documentation Standards](http://make.wordpress.org/core/handbook/inline-documentation-standards/php-documentation-standards/).
* All classes, functions, and variables are documented.
* The Plugin uses a strict file organization scheme that correspond both to the WordPress Plugin Repository structure, and that make it easy to organize the files that compose the plugin.
* The project includes a `.pot` file as a starting point for internationalization.

## Installation

The Plugin can be installed in one of two ways both of which are documented below. 

The options are:

### Copying a Directory

1. Copy the `wp-network-stats` directory into your `wp-content/plugins` directory.
2. In the WordPress Network Admin dashboard, navigation to the *Plugins* page
Locate the menu item that reads “WP Network Stats
3. Click on *Network Activate.*

### Installing a zip file

1. Create a zip of `wp-network-stats` directory.
2. In the WordPress dashboard, navigate to the *Plugins* -> *Add New* page.
3. Click Upload Plugin.
4. Upload the zip file and click Install Now.
5. Click on *Network Activate.*

## Contributing
#### Internationalizing

The WordPress Plugin Boilerplate uses a variable to store the text domain used when internationalizing strings throughout the Boilerplate. To take advantage of this method, there are tools that are recommended for providing correct, translatable files:

* [Poedit](http://www.poedit.net/)
* [makepot](http://i18n.svn.wordpress.org/tools/trunk/)
* [i18n](https://github.com/grappler/i18n)

Any of the above tools should provide you with the proper tooling to internationalize the plugin.
#### Includes

Note that if you include your own classes, or third-party libraries, there are three locations in which said files may go:

* `plugin-name/includes` is where functionality shared between the dashboard and the public-facing parts of the side reside
* `plugin-name/admin` is for all dashboard-specific functionality
* `plugin-name/public` is for all public-facing functionality

## License

The __WP Network Stats__ Plugin is licensed under the GPL v2 or later.

> This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License, version 2, as published by the Free Software Foundation.

> This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

> You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

A copy of the license is included in the root of the plugin’s directory. The file is named `LICENSE`.

## Important Notes

### Licensing

The __WP Network Stats__ Plugin is licensed under the GPL v2 or later; however, if you opt to use third-party code that is not compatible with v2, then you may need to switch to using code that is GPL v3 compatible.

For reference, [here's a discussion](http://make.wordpress.org/themes/2013/03/04/licensing-note-apache-and-gpl/) that covers the Apache 2.0 License used by [Bootstrap](http://twitter.github.io/bootstrap/).



### Assets

The `assets` directory contains three files.

1. `banner-772x250.png` is used to represent the plugin’s header image.
2. `icon-256x256.png` is a used to represent the plugin’s icon image (which is new as of WordPress 4.0).
3. `screenshot-1.png` is used to represent a single screenshot of the plugin that corresponds to the “Screenshots” heading in your plugin `README.txt`.

When committing code to the WordPress Plugin Repository, all of the banner, icon, and screenshot should be placed in the `assets` directory of the Repository, and the core code should be placed in the `trunk` directory.

# Credits

The __WP Network Stats__ plugin code was inspired or has used code from the following plugins.
1. [Network Plugin Auditor](https://wordpress.org/plugins/network-plugin-auditor/)
2. [WordPress Charts](https://wordpress.org/plugins/wp-charts/)

## Documentation, FAQs, and More

Coming Soon.
