<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Entity\Book\Score;
use App\Form\Model\BookDto;
use App\Form\Model\CategoryDto;
use App\Form\Type\BookFormType;
use App\Model\Exception\Book\BookNotFound;
use App\Model\Exception\Category\CategoryNotFound;
use App\Repository\BookRepository;
use App\Service\Category\CreateCategory;
use App\Service\Category\GetCategory;
use App\Service\FileUploader;
use JsonException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookFormProcessor
{
    public function __construct(
        private FileUploader $fileUploader,
        private FormFactoryInterface $formFactory,
        private BookRepository $bookRepository,
        private GetCategory $getCategory,
        private CreateCategory $createCategory,
        private GetBook $getBook,
        private EventDispatcherInterface $eventDispatcherInterface,
    ) {
    }

    /**
     * @throws CategoryNotFound
     * @throws BookNotFound
     * @throws JsonException
     */
    public function __invoke(Request $request, string $bookId = null): array
    {
        $book = null;
        if ($bookId === null) {
            $bookDto = BookDto::createEmpty();
        } else {
            $book = ($this->getBook)($bookId);
            $bookDto = BookDto::createFromBook($book);
            foreach ($book->getCategories() as $category) {
                $bookDto->categories[] = CategoryDto::createFromCategory($category);
            }
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $form = $this->formFactory->create(BookFormType::class, $bookDto);
        $form->submit($content);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if (!$form->isValid()) {
            return [null, $form];
        }

        $categories = [];
        foreach ($bookDto->getCategories() as $newCategoryDto) {
            $category = null;
            if ($newCategoryDto->getId() !== null) {
                $category = ($this->getCategory)($newCategoryDto->getId());
            }

            if ($category === null) {
                $category = ($this->createCategory)($newCategoryDto->getName());
            }
            $categories[] = $category;
        }

        $filename = null;
        if ($bookDto->base64Image) {
            $filename = $this->fileUploader->uploaderBase64File($bookDto->getBase64Image());
        }
        if ($book === null) {
            $book = Book::create($bookDto->getTitle(), $filename, $bookDto->getDescription(), Score::create($bookDto->getScore()), $categories);
        } else {
            $book->update(
                $bookDto->getTitle(),
                $filename ?? $book->getImage(),
                $bookDto->description,
                Score::create($bookDto->score),
                $categories
            );
        }

        $this->bookRepository->add($book);
        foreach ($book->pullDomainEvents() as $event) {
            $this->eventDispatcherInterface->dispatch($event);
        }
        return [$book, null];
    }
}
