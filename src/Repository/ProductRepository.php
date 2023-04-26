<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $entity): Product
    {
        $this->_em->persist($entity);
        $this->_em->flush();
        return $entity;
    }

    public function findBySearchTerm(string $searchTerm, $minPrice = null, $maxPrice = null, $minWeight = null, $maxWeight = null, $color = null, $size = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.name LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%');
    
        if (!empty($minPrice)) {
            $qb->andWhere('p.price >= :minPrice')
                ->setParameter('minPrice', $minPrice);
        }
    
        if (!empty($maxPrice)) {
            $qb->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $maxPrice);
        }
    
        if (!empty($minWeight)) {
            $qb->andWhere('p.weight >= :minWeight')
                ->setParameter('minWeight', $minWeight);
        }
    
        if (!empty($maxWeight)) {
            $qb->andWhere('p.weight <= :maxWeight')
                ->setParameter('maxWeight', $maxWeight);
        }
    
        if (!empty($size)) {
            $qb->andWhere('p.size = :size')
                ->setParameter('size', $size);
        }
    
        if (!empty($weight)) {
            $qb->andWhere('p.weight = :weight')
                ->setParameter('weight', $weight);
        }
    
        return $qb->getQuery()->getResult();
    }

    //  Stoğa göre filtreleme
    public function findByStockCountGreaterThan(int $count)
    {
        return $this->createQueryBuilder('p')
            ->join('p.stock', 's')
            ->where('s.stock_count > :count')
            ->setParameter('count', $count)
            ->getQuery()
            ->getResult();
    }
    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
