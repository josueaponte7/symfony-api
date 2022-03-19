<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Category
{
    private ?UuidInterface $id = null;
    private ?string $name;
    private $books;
    
    public function __construct(UuidInterface $uuid)
    {
        $this->id = $uuid;
        $this->books = new ArrayCollection();
    }
    
    public function create(): Category
    {
        return new Category(Uuid::uuid4());
    }
    
    public function getId(): UuidInterface
    {
        return $this->id;
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }
    
    public function setName(string $name): self
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getBooks(): Collection
    {
        return $this->books;
    }
    
    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addCategory($this);
        }
        
        return $this;
    }
    
    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            $book->removeCategory($this);
        }
        
        return $this;
    }
}
