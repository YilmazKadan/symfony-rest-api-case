<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractApiController
{
/**
 * @Route("", methods={"GET"})
 */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        $productCount = $productRepository->count([]);

        $this->responseArray['data'] = $products;
        $this->responseArray['count'] = $productCount;
        return $this->respond();
    }

/**
 * @Route("", methods={"POST"})
 */
    public function createProduct(Request $request, ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {

        $form = $this->buildForm(ProductType::class);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();

        // Kategori varlığı kontrol
        if (!$categoryRepository->find($product->getCategory())) {
            throw new NotFoundHttpException('Eklemeye çalıştığınız kategori bulunamadı');
        }

        $product = $productRepository->save($product);

        // Bu yapı özellikle oluşturuldu , yapı büyüdükçe ortak bir düzene ihtiyaç duyulacaktır.
        $this->responseArray['success'] = true;
        $this->responseArray['message'] = "Product ekleme işlemi başarılı";
        $this->responseArray['data'] = $product;
        return $this->respond();

    }
}
