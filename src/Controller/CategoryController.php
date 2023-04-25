<?php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CategoryController extends AbstractApiController
{
    /**
     * @Route("", methods={"POST"})
     */
    public function createCategory(Request $request, CategoryRepository $categoryRepository): Response
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

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Category $category */
        $category = $form->getData();

        // Kategori varlığı kontrol
        if ($categoryRepository->findOneBy(['name' => $category->getName()])) {
            throw new BadRequestHttpException('Category already exists');
        }

        $categoryRepository->saveCategory($category);

        // Bu yapı özellikle oluşturuldu , yapı büyüdükçe ortak bir düzene ihtiyaç duyulacaktır.
        $this->responseArray['success'] = true;
        $this->responseArray['message'] = "Kategori ekleme işlemi başarılı";
        $this->responseArray['data'] = $category;
        
        return $this->respond();

    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function updateCategory($id, Request $request, CategoryRepository $categoryRepository): Response
    {

        // return $this->respond($request);
        $form = $this->buildForm(CategoryType::class);

        // Put işleminde $form->handleRequest($request); işe yaramadığı içn manuel olarak submit işlemi gerçekleştirildi.
        $form = $this->buildForm(CategoryType::class);
        $form->handleRequest($request);
        
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Category $category */
        /** @var Category $categoryFromForm */
        $categoryFromForm = $form->getData();
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("[$id] numaralı id'ye sahip bir kategori bulunamadı");
        }

        // Kategori varlığı kontrol
        if ($existingCategory = $categoryRepository->findOneBy(['name' => $categoryFromForm->getName()])) {
            // Eğer güncellenen kategori, mevcut kategoriyle aynı değilse hata fırlat
            if ($existingCategory->getId() !== $category->getId()) {
                throw new BadRequestHttpException('Aynı isimde zaten farklı bir kategori mevcut');
            }
        }

        // Kategori varlığını güncelle
        $categoryRepository->saveCategory($category);

        $this->responseArray['message'] = "Kategori güncelleme işlemi başarılı";
        $this->responseArray['data'] = $category;

        // Güncellenen kategori varlığını yanıt olarak döndür
        return $this->respond();
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function deleteCategory($id, Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("[$id] numaralı id'ye sahip bir kategori bulunamadı");
        }
        $categoryRepository->remove($category);

        $this->responseArray['message'] = "[$id] numaralı id'ye  kategori silme işlemi başarılı";
        return $this->respond();
    }
}
