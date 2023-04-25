<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    // public function createCategory(string $name, string $description): Category
    // {
    //     $category = new Category();
    //     $category->setName($name);
    //     $category->setDescription($description);

    //     $this->_em->persist($category);
    //     $this->_em->flush();

    //     return $category;
    // }

    public function createCategory(Category $category): Category
    {
        $this->_em->persist($category);
        $this->_em->flush();

        return $category;
    }
}
