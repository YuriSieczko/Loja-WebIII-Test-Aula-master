<?php

use PHPUnit\Framework\TestCase;
use Loja\WebIII\Model\Carrinho;
use Loja\WebIII\Model\Produto;
use Loja\WebIII\Model\Usuario;

class CarrinhoTest extends TestCase
{
    private $carrinho;

    public function setUp(): void
    {
        $usuario = new Usuario('Teste');
        $this->carrinho = new Carrinho($usuario);
    }

    public function testAdicionaEContaProdutosNoCarrinho()
    {
        $produto1 = new Produto('Produto 1', 100);
        $produto2 = new Produto('Produto 2', 200);

        $this->carrinho->adicionaProduto($produto1);
        $this->carrinho->adicionaProduto($produto2);

        $totalProdutos = $this->carrinho->getTotalDeProdutos();

        self::assertEquals(2, $totalProdutos);
    }

    public function testRemoveProdutoDoCarrinho()
    {
        $produto = new Produto('Produto a ser removido', 50);

        $this->carrinho->adicionaProduto($produto);
        $this->carrinho->removeProduto($produto);

        $totalProdutos = $this->carrinho->getTotalDeProdutos();

        self::assertEquals(0, $totalProdutos);
    }

    /**
     * @dataProvider carrinhoDataProvider
     */
    public function testObterTresProdutosMaisCaros(Carrinho $carrinho, array $expected)
    {
        $result = $carrinho->getMaiores();

        $result = array_map('intval', $result);
        $expected = array_map('intval', $expected);

        self::assertEquals($expected, $result);
    }

    /**
     * @dataProvider carrinhoDataProvider2
     */
    public function testObterTresProdutosMaisBaratos(Carrinho $carrinho, array $expected)
    {
        $result = $carrinho->getMenores();

        $result = array_map('intval', $result);
        $expected = array_map('intval', $expected);

        self::assertEquals($expected, $result);
    }
    public static function carrinhoDataProvider()
    {
        $usuario = new Usuario('Teste');
        $carrinho = new Carrinho($usuario);

        // Primeiro conjunto de dados
        $carrinho1 = clone $carrinho;
        $carrinho1->adicionaProduto(new Produto('Produto 1', 100));
        $carrinho1->adicionaProduto(new Produto('Produto 2', 150));
        $carrinho1->adicionaProduto(new Produto('Produto 3', 200));
        $carrinho1->adicionaProduto(new Produto('Produto 4', 250));
        $carrinho1->adicionaProduto(new Produto('Produto 5', 300));
        $expected1 = [300, 250, 200];

        // Segundo conjunto de dados
        $carrinho2 = clone $carrinho;
        $carrinho2->adicionaProduto(new Produto('Produto 6', 120));
        $carrinho2->adicionaProduto(new Produto('Produto 7', 180));
        $carrinho2->adicionaProduto(new Produto('Produto 8', 220));
        $carrinho2->adicionaProduto(new Produto('Produto 9', 260));
        $carrinho2->adicionaProduto(new Produto('Produto 10', 320));
        $expected2 = [320, 260, 220];

        return [
            [$carrinho1, $expected1],
            [$carrinho2, $expected2],
        ];
    }

    public static function carrinhoDataProvider2()
    {
        $usuario = new Usuario('Teste');
        $carrinho = new Carrinho($usuario);

        // Primeiro conjunto de dados
        $carrinho1 = clone $carrinho;
        $carrinho1->adicionaProduto(new Produto('Produto 1', 100));
        $carrinho1->adicionaProduto(new Produto('Produto 2', 150));
        $carrinho1->adicionaProduto(new Produto('Produto 3', 200));
        $carrinho1->adicionaProduto(new Produto('Produto 4', 250));
        $carrinho1->adicionaProduto(new Produto('Produto 5', 300));
        $expected1 = [100, 150, 200];

        // Segundo conjunto de dados
        $carrinho2 = clone $carrinho;
        $carrinho2->adicionaProduto(new Produto('Produto 6', 120));
        $carrinho2->adicionaProduto(new Produto('Produto 7', 180));
        $carrinho2->adicionaProduto(new Produto('Produto 8', 220));
        $carrinho2->adicionaProduto(new Produto('Produto 9', 260));
        $carrinho2->adicionaProduto(new Produto('Produto 10', 320));
        $expected2 = [120, 180, 220];

        return [
            [$carrinho1, $expected1],
            [$carrinho2, $expected2],
        ];
    }
}
