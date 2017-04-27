# SilverStripe Asset Proxy

## Description

Attempts to fetch missing asset files from a remote host, by intercepting 404 errors.  The idea is to pull down missing asset files from staging/production sites as needed, without needing to do full syncs of the assets folder.

Unfortunately, this will NOT WORK with SilverStripe's normal image handling stuff (like eg `$Image.SetWidth`) because these don't generate image tags / URLs (needed to trigger 404s) if the images don't exist locally.

However, this can be solved by using this module in conjunction with [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles).

## Usage

After installing the module, define a `ASSETPROXY_HOST` constant in either _config.php or _ss_environment.php:

```
define('ASSETPROXY_HOST','http://my-source-server.com');
```

## Known Issues

* If the source image is changed (while retaining an identical filename) the new version will not replace the old one.


## Acknowledgements

* This module is inspired by Drupal's [Stage File Proxy](https://www.drupal.org/project/stage_file_proxy) module.
