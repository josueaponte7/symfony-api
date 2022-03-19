<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Category $entity, bool $flush = true): Category
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
        return $entity;
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Category $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    
    public function save(Category $category): Category
    {
        $this->getEntityManager()->persist($category);
        $this->getEntityManager()->flush();
        return $category;
    }
    
    public function reload(Category $category): Category
    {
        $this->getEntityManager()->refresh($category);
        return $category;
    }
    
    public function delete(Category $category): void
    {
        $this->getEntityManager()->remove($category);
        $this->getEntityManager()->flush();
    }
}
