<?php

declare(strict_types=1);

namespace Alltricks;

use PDO;
use Iterator;
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

    public function appendDatabase(int $sourceId): void
    {
        $stmt = $this->db->prepare("SELECT id, source_id, name, content
        FROM Alltricks.article;
        WHERE source_id = ?");


        $stmt->execute([$sourceId]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $this->articles[] = (object) [
                'name' => $row['name'],
                'content' => $row['content'],
                'source_id' => $row['source_id']
            ];
        }
    }

    public function appendRss(string $sourceName, string $feedUrl): void
    {
        $xml = simplexml_load_file($feedUrl);
        if ($xml !== false && isset($xml->channel)) {
            foreach ($xml->channel->item as $item) {
                $this->articles[] = (object) [
                    'name' => (string) $item->title,
                    'content' => (string) $item->description,
                    'sourceName' => $sourceName
                ];
            }
        }

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
