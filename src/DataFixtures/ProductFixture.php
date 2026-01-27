<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            // Men's Clothing
            [
                'name' => 'Classic Cotton T-Shirt',
                'slug' => 'classic-cotton-t-shirt-men',
                'description' => 'A timeless classic cotton t-shirt with a comfortable fit. Perfect for everyday wear.',
                'price' => '29.99',
                'gender' => 'men',
                'category' => 'T-Shirts',
                'stockQuantity' => 150,
                'isActive' => true,
            ],
            [
                'name' => 'Slim Fit Denim Jeans',
                'slug' => 'slim-fit-denim-jeans-men',
                'description' => 'Modern slim fit jeans crafted from premium denim. Features a classic five-pocket design.',
                'price' => '89.99',
                'gender' => 'men',
                'category' => 'Jeans',
                'stockQuantity' => 75,
                'isActive' => true,
            ],
            [
                'name' => 'Wool Blend Overcoat',
                'slug' => 'wool-blend-overcoat-men',
                'description' => 'Elegant wool blend overcoat for the modern gentleman. Perfect for formal occasions.',
                'price' => '249.99',
                'gender' => 'men',
                'category' => 'Coats',
                'stockQuantity' => 30,
                'isActive' => true,
            ],
            [
                'name' => 'Leather Chelsea Boots',
                'slug' => 'leather-chelsea-boots-men',
                'description' => 'Handcrafted leather Chelsea boots with elastic side panels. Italian design.',
                'price' => '179.99',
                'gender' => 'men',
                'category' => 'Shoes',
                'stockQuantity' => 45,
                'isActive' => true,
            ],
            [
                'name' => 'Oxford Button-Down Shirt',
                'slug' => 'oxford-button-down-shirt-men',
                'description' => 'Classic Oxford button-down shirt in premium cotton. Ideal for business or casual wear.',
                'price' => '69.99',
                'gender' => 'men',
                'category' => 'Shirts',
                'stockQuantity' => 100,
                'isActive' => true,
            ],

            // Women's Clothing
            [
                'name' => 'Floral Summer Dress',
                'slug' => 'floral-summer-dress-women',
                'description' => 'Light and breezy floral dress perfect for summer days. Features a flattering A-line silhouette.',
                'price' => '79.99',
                'gender' => 'women',
                'category' => 'Dresses',
                'stockQuantity' => 60,
                'isActive' => true,
            ],
            [
                'name' => 'High-Waisted Skinny Jeans',
                'slug' => 'high-waisted-skinny-jeans-women',
                'description' => 'Flattering high-waisted skinny jeans with stretch comfort. A wardrobe essential.',
                'price' => '84.99',
                'gender' => 'women',
                'category' => 'Jeans',
                'stockQuantity' => 90,
                'isActive' => true,
            ],
            [
                'name' => 'Cashmere Sweater',
                'slug' => 'cashmere-sweater-women',
                'description' => 'Luxuriously soft cashmere sweater in a relaxed fit. Perfect for layering.',
                'price' => '159.99',
                'gender' => 'women',
                'category' => 'Sweaters',
                'stockQuantity' => 40,
                'isActive' => true,
            ],
            [
                'name' => 'Leather Tote Bag',
                'slug' => 'leather-tote-bag-women',
                'description' => 'Spacious leather tote bag with multiple compartments. Ideal for work or weekend.',
                'price' => '129.99',
                'gender' => 'women',
                'category' => 'Bags',
                'stockQuantity' => 55,
                'isActive' => true,
            ],
            [
                'name' => 'Silk Blouse',
                'slug' => 'silk-blouse-women',
                'description' => 'Elegant silk blouse with a classic collar. Perfect for the office or evening wear.',
                'price' => '119.99',
                'gender' => 'women',
                'category' => 'Tops',
                'stockQuantity' => 70,
                'isActive' => true,
            ],
            [
                'name' => 'Stiletto Heels',
                'slug' => 'stiletto-heels-women',
                'description' => 'Classic black stiletto heels. The perfect finishing touch for any outfit.',
                'price' => '139.99',
                'gender' => 'women',
                'category' => 'Shoes',
                'stockQuantity' => 35,
                'isActive' => true,
            ],

            // Unisex Items
            [
                'name' => 'Vintage Denim Jacket',
                'slug' => 'vintage-denim-jacket-unisex',
                'description' => 'Classic vintage-style denim jacket. Timeless design that works for everyone.',
                'price' => '99.99',
                'gender' => 'unisex',
                'category' => 'Jackets',
                'stockQuantity' => 80,
                'isActive' => true,
            ],
            [
                'name' => 'Canvas Sneakers',
                'slug' => 'canvas-sneakers-unisex',
                'description' => 'Comfortable canvas sneakers with a classic look. Perfect for casual occasions.',
                'price' => '59.99',
                'gender' => 'unisex',
                'category' => 'Shoes',
                'stockQuantity' => 120,
                'isActive' => true,
            ],
            [
                'name' => 'Oversized Hoodie',
                'slug' => 'oversized-hoodie-unisex',
                'description' => 'Cozy oversized hoodie in soft cotton blend. Features a kangaroo pocket and drawstring hood.',
                'price' => '69.99',
                'gender' => 'unisex',
                'category' => 'Hoodies',
                'stockQuantity' => 100,
                'isActive' => true,
            ],
            [
                'name' => 'Leather Belt',
                'slug' => 'leather-belt-unisex',
                'description' => 'Premium leather belt with brushed metal buckle. A versatile accessory.',
                'price' => '49.99',
                'gender' => 'unisex',
                'category' => 'Accessories',
                'stockQuantity' => 200,
                'isActive' => true,
            ],
            [
                'name' => 'Wool Beanie',
                'slug' => 'wool-beanie-unisex',
                'description' => 'Warm wool beanie for cold days. One size fits all.',
                'price' => '34.99',
                'gender' => 'unisex',
                'category' => 'Accessories',
                'stockQuantity' => 150,
                'isActive' => true,
            ],

            // Some inactive products (for testing)
            [
                'name' => 'Discontinued Winter Coat',
                'slug' => 'discontinued-winter-coat',
                'description' => 'This product has been discontinued.',
                'price' => '199.99',
                'gender' => 'women',
                'category' => 'Coats',
                'stockQuantity' => 0,
                'isActive' => false,
            ],
            [
                'name' => 'Limited Edition Sneakers',
                'slug' => 'limited-edition-sneakers-men',
                'description' => 'Limited edition sneakers - currently out of stock.',
                'price' => '149.99',
                'gender' => 'men',
                'category' => 'Shoes',
                'stockQuantity' => 0,
                'isActive' => false,
            ],
        ];

        foreach ($products as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setSlug($productData['slug']);
            $product->setDescription($productData['description']);
            $product->setPrice($productData['price']);
            $product->setGender($productData['gender']);
            $product->setCategory($productData['category']);
            $product->setStockQuantity($productData['stockQuantity']);
            $product->setIsActive($productData['isActive']);
            // imagePath is left null - you can add images later through the admin panel

            $manager->persist($product);
        }

        $manager->flush();
    }
}
