<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
    
}
