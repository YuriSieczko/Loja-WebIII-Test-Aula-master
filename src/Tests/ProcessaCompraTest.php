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

    private function logResultadoTeste($mensagem)
    {
        file_put_contents('log_testes.txt', $mensagem . PHP_EOL, FILE_APPEND);
    }


    public static function carrinhoComProdutos()
    {
        // Arrange - Given
        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $pedro = new Usuario('Pedro');

        $carrinhoVazio = new Carrinho($maria);

        $carrinhoUmItem = new Carrinho($joao);
        $carrinhoUmItem->adicionaProduto(new Produto('Forno Eletrico', 4500));

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
        $carrinhoOnzeItens->adicionaProduto(new Produto('Micro-ondas', 700));

        return [
            'carrinho vazio' => [$carrinhoVazio],
            'carrinho um item' => [$carrinhoUmItem],
            'carrinho onze itens' => [$carrinhoOnzeItens],
        ];


    }

    /**
     * @dataProvider carrinhoComProdutos
     */
    public function testVerificaSe_OValorTotalDaCompraEASomaDosProdutosDoCarrinho_SaoIguais(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $totalDaCompra = $this->compra->getTotalDaCompra();

        // Assert - Then
        $totalEsperado = $carrinho->getValorTotalProdutos();

        self::assertEquals($totalEsperado, $totalDaCompra);

    }

    /**
     * @dataProvider carrinhoComProdutos
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
     * @dataProvider carrinhoComProdutos
     */
    public function testVerificaSe_OProdutoDeMaiorValorNoCarrinho_EstaCorreto(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $produtoDeMaiorValor = $this->compra->getProdutoDeMaiorValor();

        // Assert - Then
        $totalEsperado = $carrinho->getMaiorValorProduto();

        self::assertEquals($totalEsperado, $produtoDeMaiorValor);
    }

    /**
     * @dataProvider carrinhoComProdutos
     */
    public function testVerificaSe_OProdutoDeMenorValorNoCarrinho_EstaCorreto(Carrinho $carrinho)
    {
        // Act - When
        $this->compra->finalizaCompra($carrinho);

        $produtoDeMenorValor = $this->compra->getProdutoDeMenorValor();

        // Assert - Then
        $totalEsperado = $carrinho->getMenorValorProduto();

        self::assertEquals($totalEsperado, $produtoDeMenorValor);
    }

    public function testFinalizaCompraComCarrinhoVazio()
    {
        $carrinhoVazio = new Carrinho(new Usuario('Teste'));

        $resultadoCompra = $this->compra->finalizaCompra($carrinhoVazio);

        // Verifica se a compra não foi finalizada com sucesso para um carrinho vazio
        self::assertFalse($resultadoCompra);

        // Verifica se o total de produtos no carrinho vazio é zero
        $totalDeProdutos = $carrinhoVazio->getTotalDeProdutos();
        self::assertEquals(0, $totalDeProdutos);

        $resultadoTeste = $resultadoCompra ? 'FALHOU' : 'PASSOU';
        $this->logResultadoTeste('Teste Finaliza Compra com Carrinho Vazio: ' . $resultadoTeste);
    }


    public function testFinalizaCompraComApenasUmProduto()
    {
        $usuario = new Usuario('Teste');
        $produtoUnico = new Produto('Produto único', 100);
        $carrinho = new Carrinho($usuario);
        $carrinho->adicionaProduto($produtoUnico);

        $this->compra->finalizaCompra($carrinho);

        // Verifica se o total da compra é igual ao valor do único produto
        $resultadoTeste = ($produtoUnico->getValor() === $this->compra->getTotalDaCompra()) ? 'PASSOU' : 'FALHOU';

        $this->logResultadoTeste('Teste Finaliza Compra com Apenas Um Produto: ' . $resultadoTeste);

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



    public function testCompraComValorAcimaDoLimite()
    {
        $usuario = new Usuario('Teste');
        $carrinho = new Carrinho($usuario);

        // Adiciona produtos com valor total acima de 50.000,00
        $carrinho->adicionaProduto(new Produto('Produto caro', 30000));
        $carrinho->adicionaProduto(new Produto('Produto ainda mais caro', 25000));

        // Verifica se a finalização da compra retorna falso para valor acima do limite
        $resultadoTeste = $this->compra->finalizaCompra($carrinho);
        self::assertFalse($resultadoTeste);

        // Salva o resultado do teste no log
        $this->logResultadoTeste('Teste Compra com Valor Acima do Limite: ' . ($resultadoTeste ? 'PASSOU' : 'FALHOU'));
    }

}
