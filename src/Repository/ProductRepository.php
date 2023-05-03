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

    public function save(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Return array of filtered product objects
     */ 
    public function filter(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.category =:id')
            ->setParameter('id', $filters['categoryId']);

        if ($filters['brands'] != null) {
            $qb = $qb->andWhere('p.brand IN (:brand)')
            ->setParameter('brand', $filters['brands']);
        }

        if ($filters['min'] != null && $filters['min'] != '0' && $filters['min'] != '') {
            $qb = $qb->andWhere('p.price >:min')
                ->setParameter('min', $filters['min']);
        }
        
        if ($filters['max'] != null && $filters['max'] != '0' && $filters['max'] != '') {
            $qb = $qb->andWhere('p.price <:max')
                ->setParameter('max', $filters['max']);
        }
        
        switch ($filters['sort']) {
            case 'name_ASC':
                $qb->orderBy('p.name', 'ASC');
                break;
            case 'name_DESC':
                $qb->orderBy('p.name', 'DESC');
                break;
            case 'price_ASC':
                $qb->orderBy('p.price', 'ASC');
                break;
            case 'price_DESC':
                $qb->orderBy('p.price', 'DESC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Return a array of unique brands
     */
    public function fetchUniqueBrands($categoryId = null)
    {
        // return array of product objects
        $qb = $this->createQueryBuilder('p')
            ->groupBy('p.brand')
            ->orderBy('p.brand');

        // if a category is passed in arguments
        if($categoryId != null) {
            $qb = $qb->andWhere('p.category = :id')
                ->setParameter('id', $categoryId);
        }
        
        $qb = $qb->getQuery()->getResult();
        
        // return a array of brands
        $result = [];
        foreach($qb as $p){
            array_push($result, $p->getBrand());
        }

        return $result;
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
