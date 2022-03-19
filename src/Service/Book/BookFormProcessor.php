<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Service\Category\CategoryManager;
use App\Service\Category\GetCategory;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

class BookFormProcessor
{
    
    private CategoryManager $categoryManager;
    private FileUploader $fileUploader;
    private FormFactoryInterface $formFactory;
    private BookRepository $bookRepository;
    private GetCategory $getCategory;
    
    public function __construct(
        BookRepository $bookRepository,
        CategoryManager $categoryManager,
        GetCategory $getCategory,
        FileUploader $fileUploader,
        FormFactoryInterface $formFactory
    ) {
        
        $this->bookRepository = $bookRepository;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->formFactory = $formFactory;
        $this->getCategory = $getCategory;
    }
    
    public function __invoke(Book $book, Request $request): array
    {
        $bookDto = BookDto::createFromBook($book);
        
        /** @var CategoryDto[]|ArrayCollection */
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
                    $category = ($this->getCategory)($originalCategoryDto->getId());
                    //$category = $this->categoryManager->find($originalCategoryDto->getId());
                    $book->removeCategory($category);
                }
            }
            
            // Add categories
            foreach ($bookDto->getCategories() as $newCategoryDto) {
                if (!$originalCategories->contains($newCategoryDto)) {
                    $category = null;
                    if ($newCategoryDto->getId() !== null) {
                        $category = ($this->getCategory)($newCategoryDto->getId());
                        //$category = $this->categoryManager->find($newCategoryDto->getId());
                    }
                    
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDto->getName());
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