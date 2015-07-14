# Geo Detection

The module implements geo detection with calculation shipping rates.

## Install via composer

Update your `composer.json` like this

```JSON
    "require": {
        ...
        "oggettoweb/ajax": "1.*",
        "vad1ke/magento_geodetection_module": "dev-master"
        ...
    },
    "repositories": [
    ...
        {
            "type": "vcs",
            "url": "https://github.com/OggettoWeb/ajax"
        },
        {
            "type": "vcs",
            "url": "https://github.com/VaD1ke/magento_geodetection_module"
        }
    ],
    ...
    "extra":{
        "magento-root-dir": "."
    }
```

See more information about composer installer for magento at [github repository](https://github.com/magento-hackathon/magento-composer-installer/blob/master/README.md).

Don't forget to set *Allow Symlink* to *Yes* in *system->configuration->Advanced->Developer->Template settings* in admin of your magento. 