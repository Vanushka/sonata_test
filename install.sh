#!/bin/bash
echo "Run install project"

cat .env.example > .env

cat .env.example > .env.local

composer install

php bin/console doctrine:database:create

php bin/console doctrine:migrations:migrate

php bin/console doctrine:fixtures:load

export PATH="$HOME/.symfony/bin:$PATH"

mkdir var/uploads/

mkdir var/uploads/product_images

ln -s $PWD/var/uploads/ $PWD/public/

echo "Congratulations project installed!"
