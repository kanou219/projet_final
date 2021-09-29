<?php 

namespace App\Controller;


use App\Repository\ProduitRepository;
use App\MesServices\Panier\PanierService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    protected $panierService;

    public function __construct(PanierService $panierService)
    {
        $this->panierService = $panierService;
    }

    /**
     * @Route("/panier/ajouter/{id}", name="panier_ajouter")
     */
    public function ajouter(int $id,Request $request,ProduitRepository $produitRepository)
    {
        $produit = $produitRepository->find($id);

        if(!$produit)
        {
            throw $this->createNotFoundException("Le produit n'existe pas");
        }

        $this->panierService->ajouter($id);

        $routeARediriger = $request->query->get('redirige');

        if($routeARediriger)
        {
            return $this->redirectToRoute('panier_detail');
        }

        return $this->redirectToRoute('categorie_show',[
            'id' => $produit->getCategorie()->getId() 
        ]);
    }

    /**
     * @Route("/panier/detail", name="panier_detail")
     */
    public function voirMonPanier()
    {
        return $this->render("panier/detail_panier.html.twig",[
            'panier' => $this->panierService->detaillerLeContenu(),
            'total' => $this->panierService->getTotal()
        ]);
    }

    /**
     * @Route("/panier/supprimer/{id}", name="panier_supprimer_article")
     */
    public function supprimerArticle( int $id)
    {
        $this->panierService->supprimer($id);

        return $this->redirectToRoute('panier_detail');
    }

    /**
     *  @Route("/panier/diminuer/{id}", name="panier_diminuer_article")
     */
    public function diminuerArticle( int $id)
    {
        $this->panierService->diminuer($id);

        return $this->redirectToRoute('panier_detail');
    }
}