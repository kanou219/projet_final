<?php 

namespace App\MesServices\Panier;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PanierService
{
    protected $session;

    protected $produitRepository;

    public function __construct(SessionInterface $session,ProduitRepository $produitRepository)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    public function getPanier()
    {
        return $this->session->get('panier', []);
    }

    public function sauvegarderPanier($panier)
    {
        $this->session->set('panier', $panier);
    }

    public function viderPanier()
    {
        $this->sauvegarderPanier([]);
    }

    public function ajouter(int $id) 
    {
        //Je vais chercher un panier
        $panier = $this->getPanier();

        //Je verifie si j ai deja dans mon panier l article à ajouter
        foreach($panier as $item)
        {
            //Si je l ai , j augmente la quantité de 1
            if($item->getId() === $id)
            {
                $item->setQty( $item->getQty() + 1);
                $this->sauvegarderPanier($panier);
                return;
            }
        }

        //Si je ne l ai pas , j ajoute ce produit dans mon panier
        $nouveauItem = new PanierItem();
        $nouveauItem->setId($id)
            ->setQty(1);

        $panier[] = $nouveauItem;
        $this->sauvegarderPanier($panier);
        return;
    }

    public function detaillerLeContenu(): array
    {
        //Je cree un tableau vide que je vais remplir et renvoyer 
        $contenu = [];

        //Je vais chercher mon panier
        $panier = $this->getPanier();

        //Je boucle sur mon panier et ce qu il contient.
        foreach($panier as $item)
        {
            //Chaque item du panier a un Id et une Qty
            //Grace a l id , je peux recuperer le produit lié à l'id
            $produit = $this->produitRepository->find($item->getId());

            if(!$produit)
            {
            continue; 
            }

            //Je vais chercher la quantité de l item
            $qty = $item->getQty();

            //J ajoute le produit que j ai trouve en bdd et sa quantite dans une nouvelle classe
            //Une classe qui va vraiment representer le produit reel
            //J ajoute cet instance de la classe de Produit Reel dans le tableau à retourner
            $contenu[] = new PanierReelProduit($produit,$qty);
        }
        
        return $contenu;
    }

    public function getTotal()
    {
        $total = 0;

        //Je vais chercher mon panier
        $panier = $this->getPanier();

        //Je boucle sur mon panier et ce qu il contient.
        foreach($panier as $item)
        {
            $produit = $this->produitRepository->find($item->getId());

            if(!$produit)
            {
            continue; 
            }

            $total += $produit->getPrix() * $item->getQty();
        }

        return $total;
    }

    public function supprimer(int $id) 
    {
        //Je vais chercher un panier
        $panier = $this->getPanier();

        //Je verifie si j ai deja dans mon panier l article à ajouter
        foreach($panier as $item)
        {
            //Si je l ai , j augmente la quantité de 1
            if($item->getId() === $id)
            {
                $key = array_search($item,$panier);
                unset($panier[$key]);
                $this->sauvegarderPanier($panier);
                return;
            }
        }
    }

    public function diminuer(int $id)
    {
        //Je vais chercher un panier
        $panier = $this->getPanier();

        foreach($panier as $item)
        {
            if($item->getId() === $id)
            {
                $quantite = $item->getQty();

                if($quantite === 1)
                {
                    $this->supprimer($id);
                    return;
                }
                else
                {
                    $item->setQty( $item->getQty() - 1);
                    $this->sauvegarderPanier($panier);
                    return;
                }
            
            }
        }
    }
}