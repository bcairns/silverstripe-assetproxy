# SilverStripe Asset Proxy

## Description

This module can automatically fetch missing `/assets/` files on-the-fly from a remote host, alleviating the need to sync files from production or staging servers after grabbing the latest database.

It has an "Aggressive mode" (turned on by default), which fetches missing files immediately on page load.  Aggressive mode may also be disabled, in which case the module relies on passively intercepting 404 errors (which can be more scalable for many images but requires a modified approach to work properly, see below).

## Setup

After installing the module, define an `ASSETPROXY_HOST` constant in either _config.php or _ss_environment.php:

```
define('ASSETPROXY_HOST', 'http://my-source-server.com');
```

Typically you will want to have staging and dev environments pointing at production, or just dev environments pointing at staging if the site has not launched yet.

### Aggressive Mode Enabled (Default)

This is the easiest "fire and forget" drop-in solution.  However for certain applications (eg image galleries or other pages with many images) fetching all images on page load may cause prohibitive delays.  If desired, Aggressive Mode can be disabled by setting the constant `ASSETPROXY_AGGRESSIVE` to false. 


### Aggressive Mode Disabled

If Aggressive Mode is disabled, the module will attempt to fetch missing asset files by intercepting 404 errors.  This is more of a "lazy load" approach, which will not bog down the main page request, and allow files to be fetched on-demand from subsequent requests.

Unfortunately, this will NOT WORK with SilverStripe's normal image handling stuff (like eg `$Image.SetWidth`) because these don't generate image tags / URLs (needed to trigger 404s) if the images don't exist locally.

However, this can be solved by using this module in conjunction with [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles), and use either `$Image.ProfileName` or `$Image.Original` in templates.

A similar issue exists for files, with `<% if $File %>` returning false if the file does not exist locally.  To workaround this, simply check `<% if $File.Link %>` instead, which should evaluate to true even if the file does not exist.

## Planned Improvements

* Allow more granular control of Aggressive mode, on a per-class (and possibly per-field?) basis.

## Known Issues

* If the source image is changed (while retaining an identical filename) the new version will not replace the old one.  Suggested workaround is to just delete your local version of the file and let the latest be re-fetched.


## Acknowledgements

* This module is inspired by Drupal's [Stage File Proxy](https://www.drupal.org/project/stage_file_proxy) module.
