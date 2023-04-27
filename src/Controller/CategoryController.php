<?php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractApiController
{

    private $productRepository;
    private $categoryRepository;
    public function __construct(
        ProductRepository $productRepository,
        CategoryRepository $categoryRepository,

    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @Route("", methods={"GET"})
     */

    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        $categoriesArray = [];
        foreach ($categories as $category) {
            $categoriesArray[] = $this->toArray($category);
        }
        $count = $this->categoryRepository->count([]);

        $this->responseArray['data'] = $categoriesArray;
        $this->responseArray['count'] = $count;
        return $this->respond();
    }
    /**
     * @Route("", methods={"POST"})
     */
    public function createCategory(Request $request): Response
    {
        // AŞAĞIDAKİ KOD OPTİMİZE OLMAYAN BİR YAKLAŞIMA ÖRNEKTİR, FIELDER ÇOĞALDIKÇA YÖNETİMİ KARIŞACAKTIR VE İYİ BİR YAKLAŞIM DEĞİL

        // $name = $request->get('name');
        // $description = $request->get('description');

        // if (!empty($name) && !empty($description)) {

        // } else {
        //     throw new BadRequestException(
        //         'Validation error occurred',
        //     );
        // }
        // $category = new Category();
        // $category->setName($name);
        // $category->setDescription($description);

        // $category = $categoryRepository->createCategory($category);

        // return $this->json(['message' => 'Category created!', 'id' => $category->getId()]);

        // Optimize ve yönetilebilir yöntem
        $form = $this->buildForm(CategoryType::class);
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

        /** @var Category $category */
        $category = $form->getData();

        // Kategori varlığı kontrol
        if ($this->categoryRepository->findOneBy(['name' => $category->getName()])) {
            throw new BadRequestHttpException('Aynı isimde farklı bir kategori bulunmaktadır !');
        }

        $this->categoryRepository->saveCategory($category);

        // Bu yapı özellikle oluşturuldu , yapı büyüdükçe ortak bir düzene ihtiyaç duyulacaktır.
        $this->responseArray['success'] = true;
        $this->responseArray['message'] = "Kategori ekleme işlemi başarılı";
        $this->responseArray['data'] = $category;

        return $this->respond();

    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): Response
    {

        // return $this->respond($request);
        $form = $this->buildForm(CategoryType::class);

        // Put işleminde $form->handleRequest($request); işe yaramadığı içn manuel olarak submit işlemi gerçekleştirildi.
        $data = json_decode($request->getContent(), true);
        $form = $this->buildForm(CategoryType::class);
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
        /** @var Category $category */
        /** @var Category $categoryFromForm */

        $categoryFromForm = $form->getData();
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("[$id] numaralı id'ye sahip bir kategori bulunamadı");
        }

        // Kategori varlığı kontrol
        if ($existingCategory = $this->categoryRepository->findOneBy(['name' => $categoryFromForm->getName()])) {
            // Eğer güncellenen kategori, mevcut kategoriyle aynı değilse hata fırlat
            if ($existingCategory->getId() !== $category->getId()) {
                throw new BadRequestHttpException('Aynı isimde zaten farklı bir kategori mevcut');
            }
        }

        // Kategori varlığını güncelliyoruz
        $this->categoryRepository->saveCategory($category);

        $this->responseArray['message'] = "Kategori güncelleme işlemi başarılı";
        $this->responseArray['data'] = $this->toArray($category);

        // Güncellenen kategori varlığını yanıt olarak döndür
        return $this->respond();
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function findOne($id): Response
    {
        if (!$category = $this->categoryRepository->find($id)) {
            throw new NotFoundHttpException('Veritabanında bulunmayan bi id girdiniz');
        }

        $this->responseArray['data'] = $this->toArray($category);
        return $this->respond();
    }
    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deleteCategory($id, Request $request): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("[$id] numaralı id'ye sahip bir kategori bulunamadı");
        }

        // Kategoriye ait ürünleri kontrol et
        $products = $category->getProducts();
        if (!$products->isEmpty()) {
            throw new BadRequestHttpException("Bu kategoriye ait ürünler var, kategoriyi silemezsiniz.");
        }

        $this->categoryRepository->remove($category);

        $this->responseArray['message'] = "[$id] numaralı id'ye  kategori silme işlemi başarılı";
        return $this->respond();
    }

    // Bir çok yerde ihtiyaç olacağı için bir metot kullanılma gereği duydum, isterseniz helper bir sınıfa da taşınabilir
    // Burada durmasında da bir sakınca yok gibi..
    public function toArray(Category $category)
    {
        return [
            'id' => $category->getId(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
        ];
    }
}
