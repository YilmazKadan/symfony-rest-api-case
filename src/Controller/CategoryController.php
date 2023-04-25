<?php
namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
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


        
    }
}
