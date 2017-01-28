# readme

the factorial_monitoring_connector is a small module to collect various informations about hte installation on request.

## installation the old fashioened way

download the repository, put it in your modules-folder

## installation as git submodule

add this repository as a submodule via 

    git submodule add https://github.com/factorial-io/factorial_monitoring_connector.git sites/all/modules/custom/factorial_monitoring_connector

## installation via composer

Add the following lines to your `repositories`-section:

    {
      "type": "vcs",
      "url": "https://github.com/factorial-io/factorial_monitoring_connector.git"
    }


run `composer require factorial-io/factorial_monitoring_connector:dev-8.x-1.x`

## Enable the module

Enable the module as usual.

Add the host to you monitoring configuration.

