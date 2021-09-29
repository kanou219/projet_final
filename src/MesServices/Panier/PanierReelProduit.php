<?php 

namespace App\MesServices\Panier;

class PanierReelProduit
{
    public $produit;

    public $qty;

    public function __construct($produit, $qty)
    {
        $this->produit = $produit;
        $this->qty = $qty;
    }

    public function getTotal()
    {
        return $this->produit->getPrix() * $this->qty;
    }
}