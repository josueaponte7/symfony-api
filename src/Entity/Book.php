<?php

namespace App\Entity;

use App\Entity\Book\Score;
use App\Event\Book\BookCreatedEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DomainException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\EventDispatcher\Event;
use function array_key_exists;
use function in_array;


class Book
{
    private array $domainEvents = [];
    
    public function __construct(
        private UuidInterface $id,
        private string $title,
        private ?string $image = null,
        private ?string $description = null,
        private ?Score $score = new Score(),
        private ?Collection $categories = new ArrayCollection()
    ) {
    }
    
    public static function create(
        string $title,
        ?string $image,
        ?string $description,
        ?Score $score,
        array $categories
    ): self {
        $book = new self(
            Uuid::uuid4(),
            $title,
            $image,
            $description,
            $score,
            new ArrayCollection($categories)
        );
        $book->addDomainEvent(new BookCreatedEvent($book->getId()));
        return $book;
    }
    
    public function update(string $title, ?string $image, ?string $description, ?Score $score, array $categories): void
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
    
    public function patch(array $data): self
    {
        if(array_key_exists('score', $data)) {
            $this->score = Score::create($data['score']);
        }
        if(array_key_exists('title', $data)) {
            $title = $data['title'];
            if($title === null) {
                throw new DomainException('Title cannot be null');
            }
            $this->title = $title;
        }
        return $this;
    }
    
    public function addDomainEvent(Event $event): void
    {
        $this->domainEvents[] = $event;
    }
    
    public function pullDomainEvents(): array
    {
        return $this->domainEvents;
    }
    
    public function getId(): UuidInterface
    {
        return $this->id;
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
    
    public function __toString()
    {
        return $this->title ?? 'Libro';
    }
    
}
