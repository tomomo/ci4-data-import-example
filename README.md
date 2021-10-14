# CI4 data import example

## Overview
This is a sample that imports large CSV data and was created to confirm the following issues:

https://github.com/codeigniter4/CodeIgniter4/issues/4099

Use ["csv_zenkoku.zip"](http://jusyo.jp/downloads/new/csv/csv_zenkoku.zip) that can be downloaded from the site ["jusyo.jp"](http://jusyo.jp/csv/new.php).

This is CSV data for Japanese addresses.
It has about 150,000 records and is 20MB of data.
No details such as error handling have been created.

## Environment
If you can use docker, it's easy to build.

1. Match `.env` to your environment.
2. Build the environment.
    ```bash
    git clone https://github.com/tomomo/ci4-data-import-example.git
    cd ci4-data-import-example
    docker-compose up -d --build
    docker exec -it ci4-data-import-example-app composer install
    docker exec -it ci4-data-import-example-app php spark migrate --all
    ```

Check the http://localhost in your browser.
