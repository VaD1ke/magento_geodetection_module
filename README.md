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

## IP2Location Database
To get ip2location data, you need to download csv file DB3.LITE (it's free) from here (http://lite.ip2location.com/). 

Then you need to run a sql query for loading data from csv in table. Here is the query:

``` sql
  LOAD DATA LOCAL INFILE '/path/to/file.csv' INTO TABLE oggetto_geodetection_iplocations 
  FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n' 
  (ip_from, ip_to, country_code, country_name, region_name, city_name);
```

It can throw few errors or exceptions. To fix them you need to do 1,2,6,7 items in this [insruction list](http://codelinks.pachanka.org/post/72371859454/php-mysql-load-data-infile-errors ).
Also you need to run your mysql with this parameter: *--local-infile*

``` sql 
    mysql -uuser -ppassword --local-infile 
```
