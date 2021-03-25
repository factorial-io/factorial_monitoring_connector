# readme

the factorial_monitoring_connector is a small module to collect various informations about the installation on request.

## installation the old fashioened way

* download the repository
* put it in your modules-folder

## installation as git submodule (deprecated)

* add this repository as a submodule via 

        git submodule add https://github.com/factorial-io/factorial_monitoring_connector.git sites/all/modules/custom/factorial_monitoring_connector

## installation via composer

* run `composer require factorial-io/factorial_monitoring_connector:dev-8.x-2.x`

## Setup

* Enable the module as usual.
* Add the host to you monitoring configuration.
* As the module has no settings-form (yet), please export your configuration, add the file according to your needs. Re-import your configuration again.
