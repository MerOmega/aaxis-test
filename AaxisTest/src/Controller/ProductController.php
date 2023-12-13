<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    /**
     * Receives a JSON array of products and adds them to the database by calling a service which handles the logic.
     *
     * @param Request $request
     * @param ProductService $productService
     * @return JsonResponse
     */
    #[Route('/add', name: 'product_add', methods: ['POST'])]
    public function addProduct(Request $request, ProductService $productService): JsonResponse
    {
        $productsData = json_decode($request->getContent(), true);

        if (array_keys($productsData) !== range(0, count($productsData) - 1) || !is_array($productsData)) {
            return new JsonResponse([
                                        'status' => 'error',
                                        'message' => 'Invalid JSON format. JSON must be an array of objects.'
                                    ], 400);
        }

        $productsReponse           = $productService->addProducts($productsData);
        $productsWithError         = $productsReponse['productsWithError'];
        $successfullyAddedProducts = $productsReponse['successfullyAddedProducts'];

        if (count($productsWithError) === 0) {
            return new JsonResponse(['status' => 'All products added successfully!'], 201);
        } elseif (count($successfullyAddedProducts) > 0) {
            return new JsonResponse([
                                        'status' => 'Partial success',
                                        'data'   => [
                                            'added_products'  => $successfullyAddedProducts,
                                            'failed_products' => $productsWithError
                                        ]
                                    ], 200);
        } else {
            return new JsonResponse(['status' => 'Failed to add any product', 'errors' => $productsWithError], 400);
        }
    }

    /**
     * Receives a JSON array of products and updates them in the database by calling a service which handles the logic.
     *
     * @param Request $request
     * @param ProductService $productService
     * @return JsonResponse
     */
    #[Route('/update', name: 'product_update', methods: ['PATCH'])]
    public function updateProducts(Request $request, ProductService $productService): JsonResponse
    {
        $productsData  = json_decode($request->getContent(), true);

        if (array_keys($productsData) !== range(0, count($productsData) - 1) || !is_array($productsData)) {
            return new JsonResponse([
                                        'status' => 'error',
                                        'message' => 'Invalid JSON format. JSON must be an array of objects.'
                                    ], 400);
        }

        $updateResults = $productService->updateProducts($productsData);

        $productsWithError           = $updateResults['productsWithError'];
        $successfullyUpdatedProducts = $updateResults['successfullyUpdatedProducts'];

        if (count($productsWithError) === 0) {
            return new JsonResponse(['status' => 'All products updated successfully!'], 200);
        } elseif (count($successfullyUpdatedProducts) > 0) {
            return new JsonResponse([
                                        'status' => 'Partial success',
                                        'data'   => [
                                            'updated_products' => $successfullyUpdatedProducts,
                                            'failed_products'  => $productsWithError
                                        ]
                                    ], 200);
        } else {
            return new JsonResponse(['status' => 'Failed to update any product', 'errors' => $productsWithError], 400);
        }
    }

    /**
     * Returns a JSON array of all products in the database.
     *
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/list', name: 'product_list', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $products = $productRepository->findAll();
        $jsonProducts = $serializer->serialize($products, 'json');

        return new JsonResponse($jsonProducts, 200, [], true);
    }
}
