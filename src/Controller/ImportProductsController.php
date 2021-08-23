<?php
namespace App\Controller;

use phpQuery;
use App\Entity\Product;
use Cocur\Slugify\Slugify;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ImportProductsController
{
    /**
    * @var EntityManagerInterface
    */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function index()
    {
        $html = file_get_contents('https://podtrade.ru/catalog/01_sharikovye_podshipniki/');
        
        $pages = 1;
        
        if(!empty($html)){
                
            phpQuery::newDocument($html);
        
            $pages = (integer) pq('.bx-pagination-container')->find('ul li:eq(5)')->text();
        
        }
        
        for ($count = 1; $count < $pages; $count++) {
        
            $html = file_get_contents('https://podtrade.ru/catalog/01_sharikovye_podshipniki/?PAGEN_1='.$count);
        
            if(!empty($html)){
                    
                phpQuery::newDocument($html);
            
                $products = pq('.section-block-view-item-inner');
                        
                $items = [];
            
                $slugify = new Slugify();

                foreach($products as $key => $product){
            
                    $product = pq($product);
            
                    $items[$key]["id"] = (integer) str_replace(['/product/', '/'], '', $product->find('.block-view-title a')->attr('href'));
                    
                    $items[$key]["name"] = preg_replace('/[\t\t\n]/', '', $product->find('.block-view-title')->text());

                    $items[$key]["slug"] = $slugify->slugify($items[$key]["name"]);
            
                    $items[$key]["price"] =  (float) preg_replace('/[\t\t\n]/', '', $product->find('.block-view-price')->text());
            
                    $items[$key]["image"] = $product->find('.block-view-photo a img')->attr('src');

                    $content_image = file_get_contents("https://podtrade.ru/".$items[$key]["image"]);
                    
                    $prevName = explode('.', $items[$key]["image"]);

                    if($prevName[1] !== "section/podtrade_2020_list/images/no_photo") {
                        $fileName = md5(uniqid()).'.'.$prevName[1];
                        file_put_contents('var/uploads/product_images/'.$fileName, $content_image);
                    } else {
                        $fileName = "";
                    }
                    
                    $items[$key]["image"] = $fileName;

                }

                foreach($items as $item) {

                    $check_if_product_exist = $this->manager->getRepository(Product::class)->getImportIdProduct($item["id"]);

                    if(is_null($check_if_product_exist)) {

                        $product = new Product();
                        $product->setName($item["name"]);
                        $product->setPrice($item["price"]);
                        $product->setImport($item["id"]);
                        $product->setImage($item["image"]);
                        $product->setActive(true);
                        $product->setSlug($item["slug"]);
                        $product->setCreatedAt(new \DateTimeImmutable());
                        $product->setUpdatedAt(new \DateTimeImmutable());

                        $this->manager->persist($product);

                        $this->manager->flush();

                    }

                }
            
            }
        
        }

    }

}