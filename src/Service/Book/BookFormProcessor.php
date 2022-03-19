<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Entity\Book\Score;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Service\Category\CreateCategory;
use App\Service\Category\GetCategory;
use App\Service\FileUploader;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BookFormProcessor
{
    
    
    private FileUploader $fileUploader;
    private FormFactoryInterface $formFactory;
    private BookRepository $bookRepository;
    private GetCategory $getCategory;
    private CreateCategory $createCategory;
    private GetBook $getBook;
    
    public function __construct(
        BookRepository $bookRepository,
        GetCategory $getCategory,
        CreateCategory $createCategory,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory,
        GetBook $getBook
    ) {
        
        $this->bookRepository = $bookRepository;
        $this->fileUploader = $fileUploader;
        $this->formFactory = $formFactory;
        $this->getCategory = $getCategory;
        $this->createCategory = $createCategory;
        $this->getBook = $getBook;
    }
    
    public function __invoke(Request $request, string $bookId = null): array
    {
        
        
        if($bookId === null) {
            $book = Book::create();
            $bookDto = BookDto::createEmpty();
        } else {
            $book = ($this->getBook)($bookId);
            $bookDto = BookDto::createFromBook($book);
            foreach($book->getCategories() as $category) {
                $bookDto->categories[] = CategoryDto::createFromCategory($category);
            }
        }
        
        
        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if(!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if(!$form->isValid()) {
            return [null, $form];
        }
        
        $categories = [];
        foreach($bookDto->getCategories() as $newCategoryDto) {
            $category = null;
            if($newCategoryDto->getId() !== null) {
                $category = ($this->getCategory)($newCategoryDto->getId());
            }
            
            if($category === null) {
                $category = ($this->createCategory)($newCategoryDto->getName());
            }
            $categories[] = $category;
        }
        
        $filename = null;
        if($bookDto->base64Image) {
            $filename = $this->fileUploader->uploaderBase64File($bookDto->getBase64Image());
        }
        $book->update($bookDto->getTitle(), $filename, $bookDto->getDescription(), Score::create($bookDto->getScore()), ...$categories);
        
        $this->bookRepository->add($book);
        return [$book, null];
    }
}