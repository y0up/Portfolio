<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use App\Service\PictureManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/picture')]
class PictureController extends AbstractController
{
    #[Route('/', name: 'picture_index', methods: ['GET'])]
    public function index(PictureRepository $pictureRepository): Response
    {
        return $this->render('admin/picture/index.html.twig', [
            'pictures' => $pictureRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'picture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PictureManager $pictureManager): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $categories = $form['Categories']->getData()->getValues();
            foreach ($categories as $category) {
                $category->addPicture($picture);
            }

            $uploadedPicture = $form['fileName']->getData();
            
            if ($uploadedPicture) {
                $newFileName = $pictureManager->upload($uploadedPicture);
                $picture->setFileName($newFileName);
            }
            
            $entityManager->persist($picture);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'picture_show', methods: ['GET'])]
    public function show(Picture $picture): Response
    {
        return $this->render('admin/picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    #[Route('/{id}/edit', name: 'picture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Picture $picture, EntityManagerInterface $entityManager, PictureManager $pictureManager, CategoryRepository $categoryRepo): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        

        if ($form->isSubmitted() && $form->isValid()) {

            $categories = $form['Categories']->getData()->getValues();
            foreach ($categoryRepo->findAll() as $category) {
                if (!in_array($category, $categories) ) {
                    $category->removePicture($picture);
                } else {
                    $category->addPicture($picture);
                }
            }
            
            $uploadedPicture = $form['fileName']->getData();
            
            if ($uploadedPicture) {
                $newFileName = $pictureManager->upload($uploadedPicture);
                $picture->setFileName($newFileName);
                $pictureManager->delete($picture);
            }

            $entityManager->persist($picture);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'picture_delete', methods: ['POST'])]
    public function delete(Request $request, Picture $picture, EntityManagerInterface $entityManager, PictureManager $pictureManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($picture);
            $entityManager->flush();
            $pictureManager->delete($picture);
        }

        return $this->redirectToRoute('picture_index', [], Response::HTTP_SEE_OTHER);
    }
}
