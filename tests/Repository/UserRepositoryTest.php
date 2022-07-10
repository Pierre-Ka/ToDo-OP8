<?php
//
//namespace App\Tests\Repository;
//
//class UserRepositoryTest
//{
//
//}
//https://symfony.com/doc/current/testing/database.html
//// tests/Repository/ProductRepositoryTest.php
//namespace App\Tests\Repository;
//
//use App\Entity\Product;
//use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
//
//class ProductRepositoryTest extends KernelTestCase
//{
//    /**
//     * @var \Doctrine\ORM\EntityManager
//     */
//    private $entityManager;
//
//    protected function setUp(): void
//    {
//        $kernel = self::bootKernel();
//
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//    }
//
//    public function testSearchByName()
//    {
//        $product = $this->entityManager
//            ->getRepository(Product::class)
//            ->findOneBy(['name' => 'Priceless widget'])
//        ;
//
//        $this->assertSame(14.50, $product->getPrice());
//    }
//
//    protected function tearDown(): void
//    {
//        parent::tearDown();
//
//        // doing this is recommended to avoid memory leaks
//        $this->entityManager->close();
//        $this->entityManager = null;
//    }
//}