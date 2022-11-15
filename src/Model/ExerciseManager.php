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

    //SELECT n.subject_id FROM `notion` AS n 
    //INNER JOIN exercise AS e ON e.notion_id = n.id WHERE e.id=2; 

    // public function getSubjectId(int $exerciseId): string
    // {

    //     $statement = $this->pdo->prepare("SELECT n.subject_id FROM `notion` AS n INNER JOIN "
    //         . self::TABLE . " AS e ON e.notion_id = n.id WHERE e.id=:id");

    //     $statement->bindValue('id', $exerciseId, PDO::PARAM_INT);
    //     $statement->execute();

    //     return $statement->fetch()['subject_id'];
    // }

    // public function selectFirstSubject(int $themeId): array
    // {

    //     $statement = $this->pdo->prepare("SELECT MIN(id) AS id FROM " . self::TABLE . " WHERE `theme_id` = :idtheme");
    //     $statement->bindValue('idtheme', $themeId, PDO::PARAM_INT);
    //     $statement->execute();

    //     return $statement->fetchAll();
    //     // return (int)$this->pdo->lastInsertId();
    // }


    public function add(int $notionId, string $name, string $url): int
    {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (notion_id, name, url) VALUES (:notion_id, :name, :url)");

        $statement->bindValue('notion_id', $notionId, \PDO::PARAM_INT);
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('url', $url, \PDO::PARAM_STR);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }


    public function update(int $exerciseId, string $name, string $url): int
    {

        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET name = :name, url = :url WHERE id = :id");

        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('url', $url, \PDO::PARAM_STR);
        $statement->bindValue('id', (int)$exerciseId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }
}
