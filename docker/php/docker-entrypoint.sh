#!/bin/bash
set -e

# Instalacja zależności Composera
composer install

# Uruchomienie PHP-FPM
php-fpm 