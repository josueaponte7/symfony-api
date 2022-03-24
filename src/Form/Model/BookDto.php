<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Book;

class BookDto
{
    public ?string $title = null;
    public ?string $base64Image = null;
    public ?string $description = null;
    public ?int $score = null;
    /** @var CategoryDto[]|null */
    public ?array $categories = [];

    public function __construct()
    {
        $this->categories = [];
    }

    public static function createFromBook(Book $book): self
    {
        $dto = new self();
        $dto->title = $book->getTitle();
        return $dto;
    }

    public static function createEmpty(): self
    {
        return new self();
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBase64Image(): ?string
    {
        return $this->base64Image;
    }

    /**
     * @return CategoryDto[]|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }
}
