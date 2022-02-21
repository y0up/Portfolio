<?php

namespace App\Controller\Main;

use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(PictureRepository $pictureRepo, CategoryRepository $categoryRepo): Response
    {
        $pictures = $pictureRepo->findAll();
        $categories = $categoryRepo->findAll();
        return $this->render('main/index.html.twig', [
            'pictures' => $pictures,
            'categories' => $categories,
        ]);
    }

    #[Route('/{slug<^((?!login|register|verify).)*$>}', name: '{slug}')]
    public function menu($slug, CategoryRepository $categoryRepo): Response
    {
        $categories = $categoryRepo->findAll();
        $category = $categoryRepo->findOneBy(['slug' => $slug]);
        $pictures = $category->getPictures();
        return $this->render('main/index.html.twig', [
            'pictures' => $pictures,
            'categories' => $categories,
        ]);
    }
}
