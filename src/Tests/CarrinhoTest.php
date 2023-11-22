<?php

use PHPUnit\Framework\TestCase;

class CarrinhoTest extends TestCase
{
    private $carrinho;

    public function setUp(): void
    {
        $usuario = new Usuario('Teste');
        $this->carrinho = new Carrinho($usuario);
    }

    private function logResultadoTeste($mensagem)
    {
        file_put_contents('log_carrinho.txt', $mensagem . PHP_EOL, FILE_APPEND);
    }

    public function testAdicionaEContaProdutosNoCarrinho()
    {
        $produto1 = new Produto('Produto 1', 100);
        $produto2 = new Produto('Produto 2', 200);

        $this->carrinho->adicionaProduto($produto1);
        $this->carrinho->adicionaProduto($produto2);

        $totalProdutos = $this->carrinho->getTotalDeProdutos();

        self::assertEquals(2, $totalProdutos);

        // Salva o resultado do teste no log
        $this->logResultadoTeste('Teste Adiciona e Conta Produtos no Carrinho: ' . ($totalProdutos === 2 ? 'PASSOU' : 'FALHOU'));
    }

    public function testRemoveProdutoDoCarrinho()
    {
        $produto = new Produto('Produto a ser removido', 50);

        $this->carrinho->adicionaProduto($produto);
        $this->carrinho->removeProduto($produto);

        $totalProdutos = $this->carrinho->getTotalDeProdutos();

        self::assertEquals(0, $totalProdutos);

        // Salva o resultado do teste no log
        $this->logResultadoTeste('Teste Remove Produto do Carrinho: ' . ($totalProdutos === 0 ? 'PASSOU' : 'FALHOU'));
    }

    /**
     * @dataProvider carrinhoDataProvider
     */
    public function testObterTresProdutosMaisCaros(Carrinho $carrinho, array $expected)
    {
        $result = $carrinho->getMaiores();
        self::assertEquals($expected, $result);

        // Salva o resultado do teste no log
        $resultadoTeste = $this->assertEquals($expected, $result) ? 'PASSOU' : 'FALHOU';
        $this->logResultadoTeste('Teste Obter Três Produtos Mais Caros: ' . $resultadoTeste);
    }

    /**
     * @dataProvider carrinhoDataProvider
     */
    public function testObterTresProdutosMaisBaratos(Carrinho $carrinho, array $expected)
    {
        $result = $carrinho->getMenores();
        self::assertEquals($expected, $result);

        // Salva o resultado do teste no log
        $resultadoTeste = ($result === $expected) ? 'PASSOU' : 'FALHOU';
        $this->logResultadoTeste('Teste Obter Três Produtos Mais Baratos: ' . $resultadoTeste);
    }

    public function carrinhoDataProvider()
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
        $expected1 = [200, 250, 300];

        // Segundo conjunto de dados
        $carrinho2 = clone $carrinho;
        $carrinho2->adicionaProduto(new Produto('Produto 6', 120));
        $carrinho2->adicionaProduto(new Produto('Produto 7', 180));
        $carrinho2->adicionaProduto(new Produto('Produto 8', 220));
        $carrinho2->adicionaProduto(new Produto('Produto 9', 260));
        $carrinho2->adicionaProduto(new Produto('Produto 10', 320));
        $expected2 = [260, 320];

        return [
            [$carrinho1, $expected1],
            [$carrinho2, $expected2],
        ];
    }
}