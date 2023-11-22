<?php

namespace Loja\WebIII\Model;

class Produto
{
    /** @var string */
    private $descricao;
    /** @var float */
    private $valor;

    public function __construct(string $descricao, float $valor)
    {
        $this->descricao = $descricao;
	    $this->valor = $valor;
    }

    public function getProduto(): string
    {
        return $this->descricao;
    }

    public function getValor(): float
    {
        return $this->valor;
    }
}
