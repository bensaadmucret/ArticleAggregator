<?php

declare(strict_types=1);

namespace Tests;

use SimpleXMLElement;
use PHPUnit\Framework\TestCase;
use Alltricks\ArticleAggregator;

final class FakeDbConnexion
{
    public function query(string $sql): array
    {
        // Simule une requête à la base de données et retourne des données de test
        $data = [
            ['id' => 1, 'name' => 'Article 1', 'content' => 'Lorem ipsum dolor sit amet 1', 'source_id' => 1],
            ['id' => 2, 'name' => 'Article 2', 'content' => 'Lorem ipsum dolor sit amet 2', 'source_id' => 2]
        ];
        return $data;
    }
}

final class ArticleAggregatorTest extends TestCase
{
    /**
     * @var array<int, object> $articles
     */
    private array $articles = [];

    public function setUp(): void
    {
        // Initialisation des articles fictifs
        $this->articles = [
            (object) ['id' => 1, 'name' => 'Article RSS 1', 'content' => 'Contenu article RSS 1'],
            (object) ['id' => 2, 'name' => 'Article RSS 2', 'content' => 'Contenu article RSS 2'],
            (object) ['id' => 3, 'name' => 'Article RSS 3', 'content' => 'Contenu article RSS 3'],
            (object) ['id' => 4, 'name' => 'Article RSS 4', 'content' => 'Contenu article RSS 4']
        ];
    }

    public function testAppendDatabase()
    {
        $dbConnexion = new FakeDbConnexion();
        $aggregator = new ArticleAggregator($dbConnexion);

        // Appel de la méthode pour ajouter les articles de la source 1
        $aggregator->appendDatabase(1);

        // Assertions sur le contenu de l'agrégateur d'articles
        $this->assertCount(4, $aggregator);
        $this->assertEquals('Article 1', $aggregator->current()->name);
        $this->assertEquals('Lorem ipsum dolor sit amet 1', $aggregator->current()->content);
        $this->assertEquals(1, $aggregator->current()->source_id);

        $aggregator->next();
        $this->assertEquals('Article 2', $aggregator->current()->name);
        $this->assertEquals('Lorem ipsum dolor sit amet 2', $aggregator->current()->content);
        $this->assertEquals(2, $aggregator->current()->source_id);
    }

}
