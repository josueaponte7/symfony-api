<?php

namespace App\Entity;

use App\Entity\Book\Score;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use function in_array;


class Book
{
    private UuidInterface $id;
    private string $title;
    private ?string $description = null;
    private Score $score;
    private ?string $image;
    private Collection $categories;
    
    public function __construct(UuidInterface $uuid)
    {
        $this->id = $uuid;
        $this->score = Score::create();
        $this->categories = new ArrayCollection();
    }
    
    public static function create(): self
    {
        return new self(Uuid::uuid4());
    }
    
    public function getId(): UuidInterface
    {
        return $this->id;
    }
    
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle(string $title): self
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getImage(): ?string
    {
        return $this->image;
    }
    
    public function setImage(?string $image): self
    {
        $this->image = $image;
        
        return $this;
    }
    
    public function getDescription(): ?string
    {
        return $this->description;
    }
    
    public function getScore(): Score
    {
        return $this->score;
    }
    
    public function setScore(Score $score): self
    {
        $this->score = $score;
        return $this;
    }
    
    public function getCategories(): Collection
    {
        return $this->categories;
    }
    
    public function update(string $title, ?string $image, ?string $description, ?Score $score, Category ...$categories): void
    {
        $this->title = $title;
        $this->image = $image;
        $this->description = $description;
        $this->score = $score;
        $this->updateCategories(...$categories);
    }
    
    public function updateCategories(Category ...$categories): void
    {
        /** @var Category[]|ArrayCollection */
        $originalCategories = new ArrayCollection();
        foreach($this->categories as $category) {
            $originalCategories->add($category);
        }
        
        // Remove categories
        foreach($originalCategories as $originalCategory) {
            if(!in_array($originalCategory, $categories, true)) {
                $this->removeCategory($originalCategory);
            }
        }
        
        // Add categories
        foreach($categories as $newCategory) {
            if(!$originalCategories->contains(!$newCategory)) {
                $this->addCategory($newCategory);
            }
        }
    }
    
    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);
        
        return $this;
    }
    
    public function addCategory(Category $category): self
    {
        if(!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->title ?? 'Libro';
    }
}
