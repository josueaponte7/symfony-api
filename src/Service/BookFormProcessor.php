<?php

namespace App\Service;

use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

class BookFormProcessor
{
    
    private CategoryManager $categoryManager;
    private FileUploader $fileUploader;
    private FormFactoryInterface $formFactory;
    private BookRepository $bookRepository;
    
    public function __construct(
        BookRepository $bookRepository,
        CategoryManager $categoryManager,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory
    ) {
        
        $this->bookRepository = $bookRepository;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->formFactory = $formFactory;
    }
    
    public function __invoke(Book $book, Request $request): array
    {
        $bookDto = BookDto::createFromBook($book);
        
        $originalCategories = new ArrayCollection();
        foreach ($book->getCategories() as $category) {
            $categoryDto = CategoryDto::createFromCategory($category);
            $bookDto->categories[] = $categoryDto;
            $originalCategories->add($categoryDto);
        }
        
        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if ($form->isValid()) {
            // Remove categories
            foreach ($originalCategories as $originalCategoryDto) {
                if (!in_array($originalCategoryDto, $bookDto->categories, true)) {
                    $category = $this->categoryManager->find(Uuid::fromString($originalCategoryDto->id));
                    $book->removeCategory($category);
                }
            }
            
            // Add categories
            foreach ($bookDto->categories as $newCategoryDto) {
                if (!$originalCategories->contains($newCategoryDto)) {
                    $category = null;
                    if ($newCategoryDto->id !== null) {
                        $category = $this->categoryManager->find(Uuid::fromString($newCategoryDto->id));
                    }
                    
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDto->name);
                        $this->categoryManager->persist($category);
                    }
                    $book->addCategory($category);
                }
            }
            $book->setTitle($bookDto->title);
            if ($bookDto->base64Image) {
                $filename = $this->fileUploader->uploaderBase64File($bookDto->base64Image);
                $book->setImage($filename);
            }
            //$this->bookRepository->add($book);
            $this->bookRepository->save($book);
            return [$book, null];
        }
        return [null, $form];
    }
}