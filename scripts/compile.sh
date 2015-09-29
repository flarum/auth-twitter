#!/usr/bin/env bash

# This script compiles the extension so that it can be used in a Flarum
# installation. It should be run in the root directory of the extension.

base=$PWD

cd $base
composer install --prefer-dist --optimize-autoloader --ignore-platform-reqs --no-dev

cd "${base}/js/forum"
npm install
gulp --production

cd "${base}/js/admin"
npm install
gulp --production
