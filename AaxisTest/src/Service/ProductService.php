<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(private readonly EntityManagerInterface $entityManager,
                                private readonly ProductRepository $productRepository)
    {
    }

    /**
     * This method adds products to the database and returns an array with the added products and an
     * array with the products that could not be added.
     *
     * @param array $productsData
     * @return array
     */
    public function addProducts(array $productsData): array
    {
        $successfullyAddedProducts = [];
        $productsWithError         = [];

        foreach ($productsData as $productData) {
            if (empty($productData['sku']) || empty($productData['product_name'])) {
                $productsWithError[] = ['sku' => $productData['sku'], 'error' => 'SKU or product name is missing'];
                continue;
            }

            if ($this->skuExists($productData['sku'])) {
                $productsWithError[] = ['sku' => $productData['sku'], 'error' => 'SKU already exists'];
                continue;
            }

            $product = new Product();
            $product->setSku($productData['sku']);
            $product->setProductName($productData['product_name']);
            $product->setDescription($productData['description'] ?? '');
            $product->setCreatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($product);
            $successfullyAddedProducts[] = $productData['sku'];
        }

        $this->entityManager->flush();

        return [
            'successfullyAddedProducts' => $successfullyAddedProducts,
            'productsWithError' => $productsWithError,
        ];
    }

    /**
     * This method updates products in the database and returns an array with the updated products and an
     * array with the products that could not be updated.
     *
     * @param array $productsData
     * @return array
     */
    public function updateProducts(array $productsData): array
    {
        $successfullyUpdatedProducts = [];
        $productsWithError           = [];

        foreach ($productsData as $productData) {
            $sku = $productData['sku'] ?? null;
            if (!$sku) {
                $productsWithError[] = ['sku' => "No SKU", 'error' => 'SKU is missing'];
                continue;
            }

            if (!$productData['product_name']) {
                $productsWithError[] = ['sku' => $productData['sku'], 'error' => 'Product name is missing'];
                continue;
            }

            $product = $this->productRepository->findBySku($sku);
            if (!$product) {
                $productsWithError[] = ['sku' => $sku, 'error' => 'Product not found'];
                continue;
            }

            $product->setProductName($productData['product_name']);
            $product->setDescription($productData['description'] ?? '');
            $product->setUpdatedAt(new \DateTimeImmutable());

            try {
                $this->entityManager->flush();
                $successfullyUpdatedProducts[] = $productData['sku'];
            } catch (\Exception $e) {
                $productsWithError[] = ['sku' => $sku, 'error' => $e->getMessage()];
            }
        }

        return [
            'successfullyUpdatedProducts' => $successfullyUpdatedProducts,
            'productsWithError'           => $productsWithError,
        ];
    }

    /**
     * This method checks if a product with the given SKU already exists in the database.
     *
     * @param string $sku
     * @return bool
     */
    private function skuExists(string $sku): bool
    {
        return $this->productRepository->findBySku($sku) !== null;
    }
}