```
================================================================
=== OJS Private LOCKSS Network Plugin
=== Version: 2.0.0
=== Author: Chris MacDonald <chris@fcts.ca>
=== Author: Michael Joyce <ubermichael@gmail.com>
=== Author: Dimitris Efstathiou <defstat@gmail.com>
=== Last update: August 2019
================================================================
```

## About

This plugin provides a means for OJS to preserve content on a private LOCKSS
network. The plugin checks for new and modified content and provided the PLN's
terms of use are met, will communicate with the PLN's staging server to preserve
your published content automatically.

## License

This plugin is licensed under the GNU General Public License v2. See the
accompanying OJS file `docs/COPYING` for the complete terms of this license.

## System Requirements

* OJS 3.0.0 or greater.
* CURL support for PHP.
* ZipArchive support for PHP.

## Note

The primary difference between this plugin and the existing LOCKSS preservation
mechanism present in OJS is the PLN requires no registration or involvement with
the network - as long as you agree with the network's terms of use, you can
preserve your journal's content.

## Contact/Support

Please use the PKP support forum (http://forum.pkp.sfu.ca/), PKP issue
tracker (https://github.com/pkp/pkp-lib#issues) or email the authors for
support, bugfixes, or comments.

## Setting a default

By default, the PLN plugin deposits to http://pkp-pln.lib.sfu.ca. Journal
managers can change the URL on the plugin settings page. The default URL can
also be set in the OJS `config.inc.php` file by adding this configuration:
```
; Change the default PLN
[lockss]
pln_url = http://example.com
```
You will need to clear the data caches after adding or changing this setting.
There is a link to clear the caches at
`Site Administration` > `Administrative Functions`

## Version History

* 1.0	- Initial Release
* 1.0.1	- Make upgraded plugins use default settings
* 1.0.2	- Bug fixes, add terms of use acceptance to the gateway, add setting for
          default network URL. 
* 2.0.0	- Porting PLN Plugin to OJS 3.x

## Installation Instructions

We recommend installing this plugin using the Plugin Gallery within OJS. Log in
with administrator privileges, navigate to `Settings` > `Journal` > `Plugins`, and
choose the Plugin Gallery. Find the PLN plugin there and follow the
instructions to install it.

## Build Instructions

(These instructions are only necessary if you are working with the plugin
manually. If you are installing the plugin using the Plugin Gallery, they are
not necessary.)

The plugin depends on the BagIt component
(https://packagist.org/packages/scholarslab/bagit) and it incorporates it as a
Composer (https://getcomposer.org/) module. Please make sure you have Composer
installed before installing the plugin.

- Clone the repository containing the code.
- Run OJS's `php tools/upgrade.php upgrade`
- Execute `composer install` from console, being in the cloned `pln` folder.
  (This process is going to produce a `vendor` folder containing the depending
  library.)
- Enable Acron plugin and change `config.inc.php` variable `scheduled_tasks = On`
- Enable the pln plugin

## Other usefull hints / Troubleshooting hints

- The pln plugin depends on 2 database tables, namely the `pln_deposits` and `pln_deposit_objects` tables. If those tables are not in your database, try to run OJS's `php tools/upgrade.php upgrade`.
- The `plugins.generic.pln.classes.tasks.Depositor` task must be inside the `scheduled_tasks` database table. If not, try `Reload Scheduled Tasks` from the Plugins gallery area, under the Acron plugin.
- Search the plugin's logs in the `scheduledTaskLogs` folder within the OJS files directory. Files named `PKPPLNDepositorTask-*id*-*datestamp*` should be found there.
- At the scheduledTasks files, if an entry like `[*date time*] [Notice] Task process stopped.` is found, then the process seems to have exited as it should. Otherwise please check the PHP logs for more info/errors.
- If an issue fails to be packaged, it would be usefull to try and export the issue from the native import/export plugin. Possible export problems may cause the PLN Plugin to fail to send the failed content, and the native import/export plugin may display some hints on why the failure occured.

