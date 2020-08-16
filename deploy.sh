#!/bin/bash
if [[ $# != 1 ]]
then
    echo "Error: please specify remote branch"
    exit 1
fi

git fetch
git reset --hard origin/$1
composer install --no-dev -a
