<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use SimpleXMLElement;
use PHPUnit\Framework\TestCase;
use Alltricks\ArticleAggregator;



final class ArticleAggregatorTest extends TestCase
{

    private $pdo;
    private $articleAggregator;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->createTables();
        $this->articleAggregator = new ArticleAggregator($this->pdo);

    }
  

    protected function tearDown(): void
    {
        $this->pdo->exec('DROP TABLE IF EXISTS article');
        $this->pdo->exec('DROP TABLE IF EXISTS source');
        $this->pdo = null;
    }

    private function createTables(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS article (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            content TEXT,
            source_id INTEGER
        )');

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS source (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT
        )');
    }

    // Instance class ArticleAggregator
    public function testInstance(): void
    {
        $articleAggregator = new ArticleAggregator();
        $this->assertInstanceOf(ArticleAggregator::class, $articleAggregator);
    }



    // les valeurs de la base de données sont bien récupérées
    public function testGetAllArticlesFromDb(): void
    {
        $this->articleAggregator = new ArticleAggregator($this->pdo);
        $this->articleAggregator->appendRss('Le Monde', 'https://www.lemonde.fr/rss/une.xml');
        $this->articleAggregator->getAllActicles();
        $this->assertCount(103, $this->articleAggregator);
    }

   // erreur dans la récupération des articles
    public function testGetAllArticlesFromDbError(): void
    {
        $this->articleAggregator = new ArticleAggregator($this->pdo);
        $this->articleAggregator->appendRss('Le Monde', 'https://www.lemonde.fr/rss/une.xml');
        $this->articleAggregator->getAllActicles();
        $this->assertNotCount(104, $this->articleAggregator);
    }

   
    

}


