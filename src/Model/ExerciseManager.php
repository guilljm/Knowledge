<?php

namespace App\Model;

use PDO;

class ExerciseManager extends AbstractManager
{
    public const TABLE = 'exercise';

    public function selectAllByNotion(int $notionId): array
    {

        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE `notion_id` = :id");
        $statement->bindValue('id', $notionId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

 
    // public function insert(int $notionId, string $name, string $url): int
    public function insert(int $notionId, array $exercise): int
    {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (notion_id, name, url) VALUES (:notion_id, :name, :url)");

        $statement->bindValue('notion_id', $notionId, \PDO::PARAM_INT);
        $statement->bindValue('name', $exercise['name'], \PDO::PARAM_STR);
        $statement->bindValue('url', $exercise['url'], \PDO::PARAM_STR);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }


    public function update(int $exerciseId, array $exercise): int
    {

        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET name = :name, url = :url WHERE id = :id");

        $statement->bindValue('name', $exercise['name'], \PDO::PARAM_STR);
        $statement->bindValue('url', $exercise['url'], \PDO::PARAM_STR);
        $statement->bindValue('id', (int)$exerciseId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }
}
