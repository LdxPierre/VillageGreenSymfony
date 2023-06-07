<?php

namespace App\Repository;

use App\Entity\CartItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<CartItem>
 *
 * @method CartItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CartItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CartItem[]    findAll()
 * @method CartItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartItem::class);
    }

    public function save(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CartItem $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Return fusion cartItem if User and VisitorId exists, else User, else VisitorId.
     */
    public function getItems($user, $visitorId = null): array
    {
        if (isset($user) && isset($visitorId)) {
            // User cart
            $userCart = $this->findBy(['user' => $user]);
            // Visitor cart
            $visitorCart = $this->findBy(['visitorId' => $visitorId]);

            // for all items in visitor cart
            foreach ($visitorCart as $vItem) {
                $found = false;
                // if present in user cart
                foreach ($userCart as $uItem) {
                    // Add quantity
                    if ($vItem->getProduct() == $uItem->getProduct()) {
                        $found = true;
                        $newQuantity = $uItem->getQuantity() + $vItem->getQuantity();
                        $uItem->setQuantity($newQuantity);
                        $this->getEntityManager()->persist($uItem);
                        $this->getEntityManager()->remove($vItem);
                    }
                }
                // add User id and remove Visitor Id if not found
                if (!$found) {
                    $vItem->setVisitorId(null);
                    $vItem->setUser($user);
                    $this->getEntityManager()->persist($vItem);
                }
            }

            // flush changes
            $this->getEntityManager()->flush();

            return $userCart;
        } elseif (isset($user)) {
            return $this->findBy(['user' => $user]);
        } else {
            return $this->findBy(['visitorId' => $visitorId]);
        }
    }

    //    /**
    //     * @return CartItem[] Returns an array of CartItem objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CartItem
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
