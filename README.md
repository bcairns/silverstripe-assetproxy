# SilverStripe Asset Proxy

## Description

Attempts to fetch missing asset files from a remote host, by intercepting 404 errors.  The idea is to pull down missing asset files from staging/production sites as needed, without needing to do full syncs of the assets folder.

Unfortunately, this will NOT WORK with SilverStripe's normal image handling stuff (like eg `$Image.SetWidth`) because these don't generate image tags / URLs (needed to trigger 404s) if the images don't exist locally.

However, this can be solved by using this module in conjunction with [Image Profiles](https://github.com/bcairns/silverstripe-imageprofiles).

## Usage

After installing the module, you can define profiles in config.yml:

```
ImageProfiles:
  profiles:
    Small:
      - SetWidth: 100
    Medium:
      - SetWidth: 300
    Large:
      - SetWidth: 500
    PaddedRed:
      - SetWidth: 200
      - Pad: [200,200,'#f00']
```

You can then use these profiles on any Image field:

```
$Image.Small    // output <img> tag
$Image.SmallURL // just get the URL 
```

You can also use Profile and ProfileURL methods, with the profile name as the parameter:

```
$Image.Profile(Small)    // output <img> tag
$Image.ProfileURL(Small) // just get the URL 
```


## Flushing

When making any changes to the defined profiles, you must `flush` for new settings to take effect.  This will also delete images in profiles that have changed.

## Known Issues

* If the source image is changed (while retaining an identical filename) the profile versions won't be cleared and re-generated

## Planned Improvements

* Make `<img>` output better and more customizable
* Allow default `_profiles` folder to be changed

## Acknowledgements

* This module is inspired by Drupal's image handling approach (which stems from the [ImageCache](https://www.drupal.org/project/imagecache) module).
* Big thanks to [unclecheese](https://github.com/unclecheese) for help getting magic methods working via an extension (see also [Zen Fields](https://github.com/unclecheese/silverstripe-zen-fields)).
