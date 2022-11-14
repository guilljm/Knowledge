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


    public function selectFirstSubject(int $themeId): array
    {

        $statement = $this->pdo->prepare("SELECT MIN(id) AS id FROM " . self::TABLE . " WHERE `theme_id` = :idtheme");
        $statement->bindValue('idtheme', $themeId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
        // return (int)$this->pdo->lastInsertId();
    }
}
