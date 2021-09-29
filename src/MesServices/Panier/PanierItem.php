<?php 

namespace App\MesServices\Panier;

class PanierItem
{
    public $id;

    public $qty;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }
}