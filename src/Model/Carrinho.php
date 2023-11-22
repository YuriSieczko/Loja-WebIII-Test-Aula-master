<?php

namespace Loja\WebIII\Model;

class Carrinho
{
    /** @var Produto[] */
    private $produtos;
    /** @var array */
    private $ordenados;
    /** @var array */
    private $maiores;
    /** @var array */
    private $menores;
    /** @var Usuario */
    private $usuario;
    /** @var int */
    private $qtdDeProdutos;
    /** @var float */
    private $valorTotalProdutos, $menorValor, $maiorValor;

    public function __construct(Usuario $usuario)
    {
        $this->usuario = $usuario;
        $this->produtos = [];
        $this->qtdDeProdutos = 0;
        $this->valorTotalProdutos = 0;
        $this->menorValor = 0;
        $this->maiorValor = 0;
    }

    public function adicionaProduto(Produto $produto)
    {
        $this->produtos[] = $produto;
    }

    public function removeProduto(Produto $produto)
    {
        $key = array_search($produto, $this->produtos);
        if($key !== false){
            unset($this->produtos[$key]);
        }
    }

    /**
     * @return Produto[]
     */
    public function getProdutos(): array
    {
        return $this->produtos;
    }

    public function getTotalDeProdutos(): int
    {
        $this->qtdDeProdutos = count($this->produtos);
        return $this->qtdDeProdutos;
    }

    public function getValorTotalProdutos(): float
    {
        $this->calculaValoresProdutos();
        return $this->valorTotalProdutos;
    }

    public function getMenorValorProduto(): float
    {
        $this->calculaValoresProdutos();
        return $this->menorValor;
    }

    public function getMaiorValorProduto(): float
    {
        $this->calculaValoresProdutos();
        return $this->maiorValor;
    }

    public function calculaValoresProdutos(): void {
        if($this->getTotalDeProdutos() > 0){
            $this->maiorValor = $this->produtos[0]->getValor();
            $this->menorValor = $this->produtos[0]->getValor();
            foreach ($this->produtos as $produto){
                $this->valorTotalProdutos = $this->valorTotalProdutos + $produto->getValor();
                if($produto->getValor() > $this->maiorValor){
                    $this->maiorValor = $produto->getValor();
                }
                else if($produto->getValor() < $this->menorValor){
                    $this->menorValor = $produto->getValor();
                }
            }
        }
    }

    public function ordenaProdutos(): void{
        if(count($this->produtos) >= 3){
            $this->ordenados = [];
            foreach ($this->produtos as $produto){
                $this->ordenados[] = $produto->getValor();
            }
            
            sort($this->ordenados);

            $this->maiores[0] = $this->ordenados[count($this->produtos) - 3];
            $this->maiores[1] = $this->ordenados[count($this->produtos) - 2];
            $this->maiores[2] = $this->ordenados[count($this->produtos) - 1];

            $this->menores[0] = $this->ordenados[0];
            $this->menores[1] = $this->ordenados[1];
            $this->menores[2] = $this->ordenados[2];
        }
    }

    public function getMaiores(): array{
        $this->ordenaProdutos();
        return $this->maiores;
    }

    public function getMenores(): array{
        $this->ordenaProdutos();
        return $this->menores;
    }
}
