<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Form\SearchProduitType;
use App\MesServices\ImageService;
use App\Repository\ProduitRepository;
use App\Search\SearchProduit;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/", name="admin_produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $search = new SearchProduit();

        $form = $this->createForm(SearchProduitType::class, $search);

        $form->handleRequest($request);

        $products = $paginator->paginate(
            $produitRepository->findAllProduitByFilter($search),
            $request->query->getInt('page',1),
            9
        );


        return $this->render('admin/admin_produit/index.html.twig', [
            'produits' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="admin_produit_new", methods={"GET","POST"})
     */
    public function new(Request $request,ImageService $imageService): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image_upload')->getData();

            $imageService->sauvegarderImage($produit,$file);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_produit_show", methods={"GET"})
     */
    public function show(Produit $produit): Response
    {
        return $this->render('admin/admin_produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_produit_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit,ImageService $imageService): Response
    {
        $ancienneImage = $produit->getImage();


        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('image_upload')->getData();
            
            $imageService->sauvegarderImage($produit,$file);

            $imageService->supprimerImage($ancienneImage);


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_produit_delete", methods={"POST"})
     */
    public function delete(Request $request, Produit $produit,ImageService $imageService): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            
            $imageService->supprimerImage($produit->getImage());
            
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
