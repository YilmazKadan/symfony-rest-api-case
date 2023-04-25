<?php
namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractApiController
{
    /**
     * @Route("/category", methods={"POST"})
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

        $categoryRepository->createCategory($category);
        return $this->respond($category);

    }
}
