<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\Product;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $product = new Product();
        $product->setName("Кабельно-проводниковая продукция");
        $product->setPrice(15.5);
        $product->setImage("");
        $product->setSlug("kabelno-provodnikovaya-produkciya");
        $product->setCreatedAt(new \DateTimeImmutable());
        $product->setUpdatedAt(new \DateTimeImmutable());

        $manager->persist($product);

        $manager->flush();
    }
}
