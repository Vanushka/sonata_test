#!/bin/bash
echo "Run install project"

cat .env.example > .env

cat .env.example > .env.local

composer install

php bin/console doctrine:database:create --no-interaction

php bin/console doctrine:migrations:migrate --no-interaction

php bin/console doctrine:fixtures:load --no-interaction

export PATH="$HOME/.symfony/bin:$PATH"

mkdir var/uploads/

mkdir var/uploads/product_images

ln -s $PWD/var/uploads/ $PWD/public/

echo "Congratulations! project installed!"
