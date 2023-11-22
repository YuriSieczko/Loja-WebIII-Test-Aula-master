<?php

namespace Loja\WebIII\Service;

use Loja\WebIII\Model\Carrinho;
use Loja\WebIII\Model\Produto;

class ProcessaCompra 
{
    /** @var Carrinho */
    private $carrinho;
    /** @var int */
    private $totalDeProdutos;
    /** @var float */
    private $totalDaCompra;
    /** @var float */
    private $menorValor, $maiorValor;

    public function __construct()
    {
        $this->totalDaCompra = 0;
        $this->totalDeProdutos = 0;
        $this->menorValor = 0;
        $this->maiorValor = 0;
    }

    public function finalizaCompra(Carrinho $carrinho)
    {
        $this->carrinho = $carrinho;
        $produtos = $this->carrinho->getProdutos();

        if (empty($produtos)) {
            return false; // Não finaliza a compra se o carrinho estiver vazio
        }

        if (count($produtos) > 10 && $carrinho->getValorTotalProdutos() > 50000) {
            return false; // Não finaliza a compra se exceder o limite de produtos ou valor
        }

        if(count($produtos) > 0){
            $this->menorValor = $produtos[0]->getValor();
            $this->maiorValor = $produtos[0]->getValor();
        }
        foreach($produtos as $produto){
            $this->totalDaCompra = $this->totalDaCompra + $produto->getValor();
            if($produto->getValor() > $this->maiorValor){
                $this->maiorValor = $produto->getValor();
            }
            else if($produto->getValor() < $this->menorValor){
                $this->menorValor = $produto->getValor();
            }
        }
        $this->totalDeProdutos = count($produtos);

        return true;
    }

    public function getTotalDaCompra(): float
    {
        return $this->totalDaCompra;
    }

    public function getTotalDeProdutos(): int
    {
        return $this->totalDeProdutos;
    }

    public function getProdutoDeMaiorValor(): float
    {
        return $this->maiorValor;
    }
    public function getProdutoDeMenorValor(): float
    {
        return $this->menorValor;
    }

}