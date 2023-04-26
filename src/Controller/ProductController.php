<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\StockRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/products')]
class ProductController extends AbstractApiController
{

    // Her metoda(Actiona) ayrı ayrı depencency injection yapmak yerine direkt kurucu da oluşturmak daha sağlıklı
    private $productRepository;
    private $categoryRepository;
    private $stockRepository;
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,
        StockRepository $stockRepository,

    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->stockRepository = $stockRepository;
    }
/**
 * @Route("", methods={"GET"})
 */
    public function index(): Response
    {
        $products = $this->productRepository->findAll();
        foreach ($products as $product) {
            $productArray[] = $this->toArray($product);
        }
        $productCount = $this->productRepository->count([]);

        $this->responseArray['data'] = $productArray;
        $this->responseArray['count'] = $productCount;
        return $this->respond();
    }

    // 2. Madde Kategoriye göre ürünleri listeleyebilmek.
/**
 * @Route("/category/{categoryId}", methods={"GET"})
 */
    public function getProductByCategoryId($categoryId): Response
    {
        $category = $this->categoryRepository->find($categoryId); // Kategori nesnesi alınır.

        if ($category) {
            $products = $this->productRepository->findBy(['category' => $categoryId]);
            foreach ($products as $product) {
                $productArray[] = $this->toArray($product);
            }
            $productCount = count($products);
        } else {

            $productArray = [];
            $productCount = 0;
            $this->responseArray['success'] = false;
        }

        $this->responseArray['data'] = $productArray;
        $this->responseArray['count'] = $productCount;
        return $this->respond();
    }

// 4. Madde Stoğa göre ürünleri listeleyebilmek.
/**
 * @Route("/bystock/{minumumStock}", methods={"GET"})
 */
    public function getProductsByStockCount($minumumStock = 0): Response
    {
        $products = $this->productRepository->findByStockCountGreaterThan($minumumStock);
        $productArray = [];
        foreach ($products as $product) {
            $productArray[] = $this->toArray($product);
        }
        $productCount = count($products);

        $this->responseArray['data'] = $productArray;
        $this->responseArray['count'] = $productCount;
        return $this->respond();
    }

// 3. Ürün adı veya özelliklerine göre arama yapabilmek.
/**
 * @Route("/search", methods={"GET"})
 */
    public function searchAction(Request $request)
    {
        $searchTerm = $request->query->get('q');
        $minPrice = $request->query->get('min_price');
        $maxPrice = $request->query->get('max_price');
        $minWeight = $request->query->get('min_weight');
        $maxWeight = $request->query->get('max_weight');
        $color = $request->query->get('color');
        $size = $request->query->get('size');

        $products = $this->productRepository->findBySearchTerm($searchTerm, $minPrice, $maxPrice, $minWeight, $maxWeight, $color, $size);

        $productArray = [];
        foreach ($products as $product) {
            $productArray[] = $this->toArray($product);
        }
        $productCount = count($productArray);

        $this->responseArray['data'] = $productArray;
        $this->responseArray['count'] = $productCount;
        return $this->respond();
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function findOne($id): Response
    {
        if (!$product = $this->productRepository->find($id)) {
            throw new NotFoundHttpException('Veritabanında bulunmayan bi id girdiniz');
        }

        $this->responseArray['data'] = $this->toArray($product);
        return $this->respond();
    }
/**
 * @Route("", methods={"POST"})
 */
    public function createProduct(Request $request): Response
    {

        $form = $this->buildForm(ProductType::class);
        $form->handleRequest($request);

        // Burada formun validasyon sebebi ile mi yoksa farklı bir sebepten mi submit edilmediğini anlıyoruz.
        // Ve ona göre hata bastırıyoruz.
        $formControl = $this->checkFormErrorReason($form);
        if ($formControl > -1) {
            if (!$formControl) {
                throw new BadRequestException("Form gövdesinde hiç bir eleman istenenler ile uyuşmadı");
            } else {
                return $this->respond($form, Response::HTTP_BAD_REQUEST);
            }
        }

        /** @var Product $product */
        $product = $form->getData();

        $categoryId = $form->getExtraData()['category'];
        // Kategori varlığı kontrol
        if (!$category = $this->categoryRepository->find($categoryId)) {
            throw new NotFoundHttpException('Eklemeye çalıştığınız kategori bulunamadı');
        }
        // Product varlığı kontorl
        if ($this->productRepository->findOneBy(['name' => $product->getName()])) {
            throw new BadRequestException('Eklemeye çalıştığınız aynı isimde farklı bir ürün var');
        }

        // // Product varlığı kontrol
        // if ($existingProduct = $productRepository->findOneBy(['name' => $productRepository->getName()])) {
        //     // Eğer güncellenen kategori, mevcut kategoriyle aynı değilse hata fırlat
        //     if ($existingCategory->getId() !== $product->getId()) {
        //         throw new BadRequestHttpException('Aynı isimde zaten farklı bir kategori mevcut');
        //     }
        // }

        $product->setCategory($category);
        $product->setStock(null);

        $product = $this->productRepository->save($product);

        // Bu yapı özellikle oluşturuldu , yapı büyüdükçe ortak bir düzene ihtiyaç duyulacaktır.
        $this->responseArray['success'] = true;
        $this->responseArray['message'] = "Product ekleme işlemi başarılı";
        // $this->responseArray['data'] = $product;
        return $this->respond();
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function updateProduct($id, Request $request): Response
    {

        $data = json_decode($request->getContent(), true);
        $form = $this->buildForm(ProductType::class);
        $form->submit($data);
        // Burada formun validasyon sebebi ile mi yoksa farklı bir sebepten mi submit edilmediğini anlıyoruz.
        // Ve ona göre hata bastırıyoruz.
        $formControl = $this->checkFormErrorReason($form);
        if ($formControl > -1) {
            if (!$formControl) {
                throw new BadRequestException("Form gövdesinde hiç bir eleman istenenler ile uyuşmadı");
            } else {
                return $this->respond($form, Response::HTTP_BAD_REQUEST);
            }
        }

        /** @var Product $product */
        /** @var Product $productFromForm */

        $productFromForm = $form->getData();
        $product = $this->productRepository->find($id);

        if (!$product) {
            throw new NotFoundHttpException("[$id] numaralı id'ye sahip bir product bulunamadı");
        }
        $categoryId = $form->getExtraData()['category'];
        // Kategori varlığı kontrol
        if (!$category = $this->categoryRepository->find($categoryId)) {
            throw new NotFoundHttpException('Eklemeye çalıştığınız kategori bulunamadı');
        }
        // Kategori varlığı kontrol
        if ($existingProduct = $this->productRepository->findOneBy(['name' => $productFromForm->getName()])) {
            // Eğer güncellenen kategori, mevcut kategoriyle aynı değilse hata fırlat
            if ($existingProduct->getId() !== $product->getId()) {

                throw new BadRequestException('Aynı isimde zaten farklı bir kategori mevcut');
            }
        }

        // Kategori varlığını güncelliyoruz
        $this->productRepository->save($product);

        $this->responseArray['message'] = "Product güncelleme işlemi başarılı";
        $this->responseArray['data'] = $this->toArray($product);

        // Güncellenen kategori varlığını yanıt olarak döndür
        return $this->respond();
    }

    /**
     * @Route("/{id}/stock", methods={"POST"})
     */
    public function stockUpdateOrCreate(Request $request, $id): Response
    {
        $body = json_decode($request->getContent(), true);
        if (empty($id) || empty($stockCount = $body['stockCount'])) {
            throw new BadRequestException("Hiçbir alanı boş bırakmayınız");
        }

        if (!$product = $this->productRepository->find($id)) {
            throw new NotFoundHttpException('Veritabanında bulunmayan bi id girdiniz');
        }

        if ($product->getStock() == null) {

            $stock = new Stock;
            $stock->setProduct($product);
            $stock->setStockCount($stockCount);
            $this->stockRepository->save($stock);

            $product->setStock($stock);
            $this->productRepository->save($product);
        } else {
            $stock = $product->getStock();
            $stock->setStockCount($stockCount);
            $this->stockRepository->save($stock);
        }

        $this->responseArray['success'] = true;
        $this->responseArray['message'] = "Stok güncelleme işlemi başarılı";
        return $this->respond();
    }

    // Bir çok yerde ihtiyaç olacağı için bir metot kullanılma gereği duydum, isterseniz helper bir sınıfa da taşınabilir
    // Burada durmasında da bir sakınca yok gibi..
    public function toArray(Product $product)
    {
        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'size' => $product->getSize(),
            'color' => $product->getColor(),
            'weight' => $product->getWeight(),
            'category' => [
                'id' => $product->getCategory()->getId(),
                'name' => $product->getCategory()->getName(),
            ],
            'stock' => [
                'miktar' => $product->getStock() ? $product->getStock()->getStockCount() : 0,
            ],
            'price' => $product->getPrice(),
        ];
    }
}
