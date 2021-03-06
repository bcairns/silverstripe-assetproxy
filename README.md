# SilverStripe Asset Proxy

## Description

Attempts to fetch missing asset files from a remote host, by intercepting 404 errors.  The idea is to download missing asset files from staging/production sites on-demand, without needing to do full syncs of the assets folder.

Unfortunately, out-of-the-box this will NOT WORK with SilverStripe's default image handling, because these don't generate image tags / URLs (needed to trigger 404s) if the images don't exist locally.  There are two methods for getting images to work, see Methods under Usage.

There is a similar issue with File objects; the template expression `<% if $File %>` will return false if the file does not exist locally, so it is suggested to use something like `<% if $File.ID %>` instead, to test if a file link should be displayed.

## Usage

After installing the module, define an `ASSETPROXY_SOURCE` constant in either _config.php or _ss_environment.php:

```
define('ASSETPROXY_SOURCE','http://my-source-server.com');
```

### Method 1 - Automatic Image Handling

For automatic image handling, the core `Image` and `Image_Cached` classes need to be replaced with AssetProxy's extended versions:
 
```
Injector:
  Image:
    class: AssetProxy_Image
  Image_Cached:
    class: AssetProxy_Image_Cached
```

This overrides the methods necessary to make AssetProxy work automatically (unfortunately this is not possible via Extensions).

### Method 2 - Use Image Profiles module

Instead of replacing the core `Image` classes (which may conflict with certain setups) you can use this module in conjunction with [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles), and template code like `$Image.Original` or `$Image.ProfileName`, which will always output an image tag and subsequently trigger the download via 404 handling.

This method is highly recommended as it has other benefits, such as not handling image manipulation on the main page request (which can cause delays in page load time).  However it does require a modified approach to how you deal with images in templates.


## Suggested Modules

* [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles) - A "non-blocking" approach to image handling, using configurable profiles.  If you want to use Method 2 above, this module is a requirement. 
* [Backup/Restore](https://github.com/bcairns/silverstripe-backuprestore) - Super simple downloading/uploading MySQL DB dump files from within the CMS.

Using these three modules together makes for environment-sync heaven!  Effortlessly sync the database from production (or staging), and any missing images & files will automagically show up on-demand.

## Known Issues

* If the source image is changed (while retaining an identical filename) the new version will not replace the old one.  Suggested workaround is to just delete your local version of the file and let the latest be re-fetched.


## Acknowledgements

* This module is inspired by Drupal's [Stage File Proxy](https://www.drupal.org/project/stage_file_proxy) module.
