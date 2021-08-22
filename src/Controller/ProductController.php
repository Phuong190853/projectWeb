<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/customer/product", name="cus_product")
     */
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            'product/customerProduct.html.twig', 
            [
                'products' => $products
            ]);
    }

    /**
     * @Route("/product", name="product")
     */
    public function indexProduct(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render(
            'product/index.html.twig', 
            [
                'products' => $products
            ]);
    }

    /**
     * @Route("/product/create", name="product_create")
     */
    public function prodCreate(Request $request, ProductRepository $p){
        $product = new Product();
        $form  = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $productInDB = $p->findAll();
            $newName = $product->getName();
            foreach($productInDB as $old){
                if ($old->getName() == $newName){
                    $this->addFlash('Warning', "Psroduct name already exist!");
                    return $this->redirectToRoute('product');
                }
            }


            //lấy ảnh từ uploaded file
            $image = $product->getImage();

            //tạo một unique name cho ảnh
            $fileName = md5(uniqid());
            //lấy đuôi ảnh
            $fileExtension = $image->guessExtension();
            //gộp tên ảnh và đuôi ảnh
            $imgName = $fileName . '.' . $fileExtension;

            //di chuyển ảnh vào thư mục
            try{
                $image->move(
                    $this->getParameter('product_image'), $imgName
                );
            } catch (FileException $e) {
                throwException($e);
            } 

            //lưu tên ảnh vào database
            $product->setImage($imgName);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute('product');
        }

        return $this->render(
            'product/create.html.twig',
            [
                'form'=>$form->createView()
            ]
        );
    }

    /**
     * @Route("/product/update/{id}", name="product_update")
     */
    public function prodUpdate(Request $request, $id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form  = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $uploadFile = $form['image']->getData();
            if ($uploadFile != null) {
                //lấy ảnh từ uploaded file
                $image = $product->getImage();

                //tạo một unique name cho ảnh
                $fileName = md5(uniqid());
                //lấy đuôi ảnh
                $fileExtension = $image->guessExtension();
                //gộp tên ảnh và đuôi ảnh
                $imgName = $fileName . '.' . $fileExtension;

                //di chuyển ảnh vào thư mục
                try{
                    $image->move(
                        $this->getParameter('product_image'), $imgName
                    );
                } catch (FileException $e) {
                    throwException($e);
                } 

                //lưu tên ảnh vào database
                $product->setImage($imgName);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('product');
        } else {
            if($product == null){
                return $this->redirectToRoute('product');
            }
        }

        return $this->render(
            'product/update.html.twig',
            [
                'form'=>$form->createView()
            ]
        );
    }

    /**
     *  @Route("/product/delete/{id}", name="product_delete" )
     */
    public function prodDelete($id){
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        //id is invalid
        if ($product == null) {
            $this->addFlash("Error", "Delete failed because ID is invalid");
            return $this->redirectToRoute('product');
        }

        //id is valid
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();

        return $this->redirectToRoute('product');
    }
}
