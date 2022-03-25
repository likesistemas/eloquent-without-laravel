<?php

namespace Like\Database\Tests;

use Like\Database\Eloquent;
use Like\Database\Tests\FakerProviders\ProdutoProvider;
use Like\Database\Tests\Models\Produto;
use Like\Database\Tests\Models\Subcategoria;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

class EloquentTest extends TestCase {
	public static function set_up_before_class() {
		$config = new Config();
		Eloquent::init($config);
	}

	public function testSimpleFactory() {
		$subcategoria = Eloquent::factoryOf(Subcategoria::class)->create();
		$this->assertInstanceOf(Subcategoria::class, $subcategoria);
		
		$produto = Eloquent::factoryOf(Produto::class)->create([
			'codigoSubcategoria' => $subcategoria->codigo,
		]);
		$this->assertInstanceOf(Produto::class, $produto);
		$this->assertEquals($subcategoria->codigo, $produto->subcategoria->codigo);
	}

	public function testMultipleFactoryWithState() {
		$subcategorias = Eloquent::factoryOf(Subcategoria::class, 5)->create();
		$this->assertCount(5, $subcategorias);
		foreach ($subcategorias as $subcategoria) {
			$this->assertInstanceOf(Subcategoria::class, $subcategoria);
		}
		
		$produtos = Eloquent::factoryOf(Produto::class, 5)->states(Produto::REFRIGERANTE)->create([
			'codigoSubcategoria' => $subcategorias[0]->codigo,
		]);
		$this->assertCount(5, $produtos);

		foreach ($produtos as $produto) {
			$this->assertInstanceOf(Produto::class, $produto);
			$this->assertTrue(in_array($produto->nome, ProdutoProvider::REFRIGERANTES));
		}
	}
}