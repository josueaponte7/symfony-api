<?php

namespace App\Controller\Api;


use App\Form\Model\CategoryDto;
use App\Form\Type\CategoryFormType;
use App\Service\Category\CategoryManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractFOSRestController
{
    /**
     * @Rest\Get(path="/categories")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function list(CategoryManager $categoryManager): array
    {
        return $categoryManager->getRepository()->findAll();
    }
    
    /**
     * @Rest\Post(path="/categories")
     * @Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     */
    public function createCategory(Request $request, CategoryManager $categoryManager)
    {
        $categoryDto = new CategoryDto();
        $form = $this->createForm(CategoryFormType::class, $categoryDto);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category = $categoryManager->create();
            $category->setName($categoryDto->name);
            $categoryManager->save($category);
            return $category;
        }
        return $form;
    }
}