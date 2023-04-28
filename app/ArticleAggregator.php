<?php

declare(strict_types=1);

namespace Alltricks;

use PDO;
use Iterator;
use Exception;
use Alltricks\dbConnexion;

/**
 * @implements Iterator<int, object>
 */
final class ArticleAggregator implements Iterator
{   /**
    * @var array<int, object> $articles
    */
    private array $articles = [];
    private int $position = 0;
    private PDO $db;

    public function __construct()
    {
        $dbConnexion = new dbConnexion();
        $this->db = $dbConnexion->getPdo();
    }



    public function getAllActicles(): void
    {
        $stmt = $this->db->prepare('SELECT * FROM article');
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $article = (object) [
                'name' => $row['name'],
                'content' => $row['content'],
                'source_id' => $row['source_id']
            ];
            $this->articles[] = $article;
        }
    }


    public function appendRss(string $sourceName, string $rssUrl): void
    {
        $rss = simplexml_load_file($rssUrl);
        if ($rss === false) {
            throw new Exception('Impossible de charger le flux RSS');
        }

        $sourceId = $this->getSourceIdByName($sourceName);
        if ($sourceId === null) {
            $sourceId = $this->createSource($sourceName);
        }

        foreach ($rss->channel->item as $item) {
            $articleName = $this->db->quote((string) $item->title);
            $articleContent = $this->db->quote((string) $item->description);

            // Check if article already exists
            $stmt = $this->db->prepare('SELECT id FROM article WHERE source_id = :sourceId AND name = :name');
            $stmt->execute(['sourceId' => $sourceId, 'name' => $articleName]);
            $existingArticle = $stmt->fetch();

            if ($existingArticle) {
                // Update existing article
                $stmt = $this->db->prepare('UPDATE article SET content = :content WHERE id = :id');
                $stmt->execute(['id' => $existingArticle['id'], 'content' => $articleContent]);
            } else {
                // Insert new article
                $stmt = $this->db->prepare('INSERT INTO article (source_id, name, content) VALUES (:sourceId, :name, :content)');
                $stmt->execute(['sourceId' => $sourceId, 'name' => $articleName, 'content' => $articleContent]);
            }
        }
    }


    public function getSourceById(int $sourceId): ?object
    {
        $stmt = $this->db->prepare('SELECT * FROM source WHERE id = :id');
        $stmt->bindParam(':id', $sourceId, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return (object) [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    private function createSource(string $name): string | false
    {
        $stmt = $this->db->prepare('INSERT INTO source (name) VALUES (:name)');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();


        return $this->db->lastInsertId();
    }

    public function deleteSource(int $sourceId): void
    {
        $stmt = $this->db->prepare('DELETE FROM source WHERE id = :id');
        $stmt->bindParam(':id', $sourceId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getSourceIdByName(string $name): ?int
    {
        $stmt = $this->db->prepare('SELECT id FROM source WHERE name = :name');
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return (int) $row['id'];
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function current(): object
    {
        return $this->articles[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function valid(): bool
    {
        return isset($this->articles[$this->position]);
    }
}
