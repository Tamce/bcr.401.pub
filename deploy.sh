#!/bin/bash

git fetch
git reset --hard origin/master
composer install --no-dev -a
