<?php

declare (strict_types=1);

namespace Alltricks;

use PDO;
use Iterator;

final class ArticleAggregator implements Iterator
{
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
        $stmt = $this->db->prepare("SELECT article.name, article.content, source.name AS sourceName FROM article JOIN source ON article.source_id = source.id WHERE source.id = ?");
        $stmt->execute([$sourceId]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $this->articles[] = (object) [
                'name' => $row['name'],
                'content' => $row['content'],
                'sourceName' => $row['sourceName']
            ];
        }
    }

    public function appendRss(string $sourceName, string $feedUrl): void
    {
        $xml = simplexml_load_file($feedUrl);

        foreach ($xml->channel->item as $item) {
            $this->articles[] = (object) [
                'name' => (string) $item->title,
                'content' => (string) $item->description,
                'sourceName' => $sourceName
            ];
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
