<?php

namespace App\Model;

use PDO;

class SubjectManager extends AbstractManager
{
    public const TABLE = 'subject';

    public function selectAllByTheme(int $themeId): array
    {

        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE `theme_id` = :idtheme");
        $statement->bindValue('idtheme', $themeId, PDO::PARAM_INT);
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
