<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{


    /**
     *  @Route("/category", name="category" )
     */
    public function catDetails() {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render(
            "category/index.html.twig",
            [
                "categories" => $categories
            ]
        );
    }

    /**
     *  @Route("/category/create", name="cat_create" )
     */
    public function catCreate(Request $request, CategoryRepository $c) {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $categoryInDB = $c->findAll();
            $newName = $category->getCatName();
            foreach($categoryInDB as $old){
                if ($old->getCatName() == $newName){
                    $this->addFlash('Warning', "Category name already exist!");
                    return $this->redirectToRoute('category');
                }
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category');
        }

        return $this->render(
            "category/create.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     *  @Route("/category/update/{id}", name="cat_update" )
     */
    public function catUpdate(Request $request, $id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category');
        } else {
            if ($category == null) {
                return $this->redirectToRoute('category');
            }
        }

        return $this->render(
            "category/update.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     *  @Route("/category/delete/{id}", name="cat_delete" )
     */
    public function catDelete($id){
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);

        //id is invalid
        if ($category == null) {
            return $this->redirectToRoute('category');
        }

        $check = $category->getProducts();
        if(count($check) > 0){
            $this->addFlash('warn', 'Category contains products');
            return $this->redirectToRoute('category');
        }

        //id is valid
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash('Infor', 'Delete succeed!');
        
        
        return $this->redirectToRoute('category');
    }
}
