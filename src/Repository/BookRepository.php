<?php

namespace App\Repository;

use App\Entity\Book;
use App\Model\Book\BookRepositoryCriteria;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @extends ServiceEntityRepository
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    
    public function add(Book $entity, bool $flush = true): Book
    {
        $this->_em->persist($entity);
        if($flush) {
            $this->_em->flush();
        }
        return $entity;
    }
    
    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if($flush) {
            $this->_em->flush();
        }
    }
    
    
    public function reload(Book $book): Book
    {
        $this->_em->refresh($book);
        return $book;
    }
    
    public function delete(Book $book): void
    {
        $this->_em->remove($book);
        $this->_em->flush();
        
    }
    
    public function findByCriteria(BookRepositoryCriteria $criteria): array
    {
        $queryBuilder = $this->createQueryBuilder('b')
            ->orderBy('b.title', 'DESC');
        
        if($criteria->categoryId !== null) {
            $queryBuilder
                ->andWhere(':categoryId MEMBER OF b.categories')
                ->setParameter('categoryId', $criteria->categoryId);
        }
        
        
        $queryBuilder->setMaxResults($criteria->itemsPerPage);
        $queryBuilder->setFirstResult(($criteria->page - 1) * $criteria->itemsPerPage);
        
        $paginator = new Paginator($queryBuilder->getQuery());
        return [
            'total' => count($paginator),
            'itemsPerPage' => $criteria->itemsPerPage,
            'page' => $criteria->page,
            'data' => iterator_to_array($paginator->getIterator()),
        ];
    }
    
}
