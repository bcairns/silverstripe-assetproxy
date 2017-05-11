# SilverStripe Asset Proxy

## Description

Attempts to fetch missing asset files from a remote host, by intercepting 404 errors.  The idea is to pull down missing asset files from staging/production sites as needed, without needing to do full syncs of the assets folder.

Unfortunately, this will NOT WORK with SilverStripe's normal image handling stuff (like eg `$Image.SetWidth`) because these don't generate image tags / URLs (needed to trigger 404s) if the images don't exist locally.

However, this can be solved by using this module in conjunction with [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles), and template code like `$Image.Original` or `$Image.ProfileName`, which will always output an image tag and subsequently trigger the download via 404 handling.

There is a similar issue with File objects; the template expression `<% if $File %>` will return false if the file does not exist locally, so it is suggested to use `<% if $File.Link %>` instead, to test if a file link should be displayed.

## Usage

After installing the module, define an `ASSETPROXY_HOST` constant in either _config.php or _ss_environment.php:

```
define('ASSETPROXY_HOST','http://my-source-server.com');
```

## Suggested Modules

* [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles) - A "non-blocking" approach to image handling, using configurable profiles.  Using this module is effectively a requirement if you want AssetProxy to handle image fields.
* [Backup/Restore](https://github.com/bcairns/silverstripe-backuprestore) - Super simple downloading/uploading MySQL DB dump files from within the CMS.

Using these three modules together makes for environment-sync heaven!  Effortlessly sync the database from production (or staging), and any missing images & files will automagically show up on-demand.

## Known Issues

* If the source image is changed (while retaining an identical filename) the new version will not replace the old one.  Suggested workaround is to just delete your local version of the file and let the latest be re-fetched.


## Acknowledgements

* This module is inspired by Drupal's [Stage File Proxy](https://www.drupal.org/project/stage_file_proxy) module.
