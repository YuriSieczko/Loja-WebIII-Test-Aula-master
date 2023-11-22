<?php

use Loja\WebIII\Model\Carrinho;
use Loja\WebIII\Model\Produto;
use Loja\WebIII\Model\Usuario;
use Loja\WebIII\Service\ProcessaCompra;
use PHPUnit\Framework\TestCase;

class ProcessaCompraTest extends TestCase
{
    private $compra;

    public function setUp(): void
    {
        $this->compra = new ProcessaCompra();
    }


    public static function carrinhoComProdutos3()
    {
        $pedro = new Usuario('Pedro');

        $carrinhoOnzeItens = new Carrinho($pedro);
        $carrinhoOnzeItens->adicionaProduto(new Produto('Geladeira', 1500));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Forno Eletrico', 4500));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Pia', 500));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Freezer', 2000));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Cooktop', 600));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Fogao', 1000));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Cadeiras Jantar', 500));
        $carrinhoOnzeItens->adicionaProduto(new Produto('Air Fryer', 200));
        $produto = new Produto('Mesa Jantar', 500);
        $carrinhoOnzeItens->adicionaProduto($produto);
        $carrinhoOnzeItens->removeProduto($produto);
        $carrinhoOnzeItens->adicionaProduto($produto);
        $carrinhoOnzeItens->adicionaProduto(new Produto('Talheres', 150));


        return [
            'carrinho onze itens' => [$carrinhoOnzeItens],
        ];

    }

    /**
     * @dataProvider carrinhoComProdutos3
     */
    public function testVerificaSe_OValorTotalDaCompraEASomaDosProdutosDoCarrinho_SaoIguais(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $totalDaCompra = $this->compra->getTotalDaCompra();
        // echo "Total da compra até agora: " . $totalDaCompra . "\n";
        // Assert - Then
        $totalEsperado = $carrinho->getValorTotalProdutos();
        // echo "Total da compra até agora: " . $totalEsperado . "\n";

        self::assertEquals($totalEsperado, $totalDaCompra);

    }

    /**
     * @dataProvider carrinhoComProdutos3
     */
    public function testVerificaSe_AQuantidadeDeProdutosEmCompraECarrinho_SaoIguais(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $totalDeProdutosDaCompra = $this->compra->getTotalDeProdutos();

        // Assert - Then
        $totalEsperado = $carrinho->getTotalDeProdutos();

        self::assertEquals($totalEsperado, $totalDeProdutosDaCompra);
    }

    /**
     * @dataProvider carrinhoComProdutos3
     */
    public function testVerificaSe_OProdutoDeMaiorValorNoCarrinho_EstaCorreto(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $produtoDeMaiorValor = $this->compra->getProdutoDeMaiorValor();

        // Assert - Then
        $totalEsperado = $carrinho->getMaiorValorProduto();

        // echo "Esperado: " . $totalEsperado . "Maior" . $produtoDeMaiorValor . "\n";
        self::assertEquals($totalEsperado, $produtoDeMaiorValor);
    }

    /**
     * @dataProvider carrinhoComProdutos3
     */
    public function testVerificaSe_OProdutoDeMenorValorNoCarrinho_EstaCorreto(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $produtoDeMenorValor = $this->compra->getProdutoDeMenorValor();

        // Assert - Then
        $totalEsperado = $carrinho->getMenorValorProduto();
        // echo "Esperado: " . $totalEsperado . "MENOR" . $produtoDeMenorValor . "\n";
        self::assertEquals($totalEsperado, $produtoDeMenorValor);
    }

    /**
     * @dataProvider carrinhosVazios
     */
    public function testFinalizaCompraComCarrinhoVazio($carrinho)
    {
        $resultadoCompra = $this->compra->finalizaCompra($carrinho);

        // Verifica se a compra não foi finalizada com sucesso para um carrinho vazio
        self::assertFalse($resultadoCompra);

        // Verifica se o total de produtos no carrinho vazio é zero
        $totalDeProdutos = $carrinho->getTotalDeProdutos();
        self::assertEquals(0, $totalDeProdutos);

    }

    public static function carrinhosVazios()
    {
        $carrinhoVazio1 = new Carrinho(new Usuario('Teste1'));
        $carrinhoVazio2 = new Carrinho(new Usuario('Teste2'));

        return [
            'carrinho vazio 1' => [$carrinhoVazio1],
            'carrinho vazio 2' => [$carrinhoVazio2],
        ];
    }


    public function testFinalizaCompraComApenasUmProduto()
    {
        $usuario = new Usuario('Teste');
        $produtoUnico = new Produto('Produto único', 100);
        $carrinho = new Carrinho($usuario);
        $carrinho->adicionaProduto($produtoUnico);

        $this->compra->finalizaCompra($carrinho);

        // Verifica se o total da compra é igual ao valor do único produto
        self::assertEquals($produtoUnico->getValor(), $this->compra->getTotalDaCompra());
    }

    public static function carrinhosComMaisDeDezItens()
    {
        $usuario = new Usuario('Teste');
        $carrinhos = [];

        for ($i = 0; $i < 3; $i++) {
            $carrinho = new Carrinho($usuario);
            for ($j = 0; $j < 11; $j++) {
                $carrinho->adicionaProduto(new Produto("Produto $j", 1000));
            }
            $carrinhos[] = [$carrinho];
        }

        return $carrinhos;
    }

    /**
     * @dataProvider carrinhosComMaisDeDezItens
     */
    public function testCompraComMaisDeDezItens(Carrinho $carrinho)
    {
        $resultadoTeste = $this->compra->finalizaCompra($carrinho);
        self::assertFalse($resultadoTeste);
    }



    public static function carrinhosComValoresAltos(): array
    {
        $usuario = new Usuario('Teste');

        $carrinhoValorAlto = new Carrinho($usuario);
        $carrinhoValorAlto->adicionaProduto(new Produto('Produto caro', 30000));
        $carrinhoValorAlto->adicionaProduto(new Produto('Produto ainda mais caro', 25000));

        return [
            'carrinho com valor acima do limite' => [
                $carrinhoValorAlto,
                false, // Espera-se que a compra não seja finalizada com sucesso
            ],
        ];
    }

    /**
     * @dataProvider carrinhosComValoresAltos
     */
    public function testCompraComValorAcimaDoLimite(Carrinho $carrinho, bool $esperado)
    {
        $resultadoTeste = $this->compra->finalizaCompra($carrinho);

        self::assertEquals($esperado, $resultadoTeste);

    }


}
