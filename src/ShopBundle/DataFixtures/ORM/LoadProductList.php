<?php

    namespace ShopBundle\DataFixtures\ORM;
    use Doctrine\Common\DataFixtures\FixtureInterface;
    use Doctrine\Common\Persistence\ObjectManager;
    use ShopBundle\Entity\Product;

class LoadProductList implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for($i=1;$i<16;$i++)
        {
            $product = new Product();
            $product->setName('product'.$i);
            $product->setDescription('super product '.$i);
            $product->setPrice(99);
            $product->setStorage(10);
            $manager->persist($product);
            $manager->flush();
        }
    }
}