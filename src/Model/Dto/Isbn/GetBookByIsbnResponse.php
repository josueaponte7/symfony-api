<?php

namespace App\Model\Dto\Isbn;

class GetBookByIsbnResponse
{
    private int $numberOfPages;
    private string $title;
    private string $publishDate;
    
    
    public function __construct(int $numberOfPages, string $title, string $publishDate)
    {
        $this->numberOfPages = $numberOfPages;
        $this->title = $title;
        $this->publishDate = $publishDate;
    }
    
    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }
    
    
    public function getTitle(): string
    {
        return $this->title;
    }
    
    
    public function getPublishDate(): string
    {
        return $this->publishDate;
    }
    
    
}