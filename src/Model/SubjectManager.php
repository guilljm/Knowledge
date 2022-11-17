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

    public function getName(string $name, int $themeId): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name = :name and theme_id = :themeid");
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('themeid', $themeId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function add(int $themeId, string $name): int
    {
        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (name, theme_id) VALUES (:name, :theme_id)");

        $statement->bindValue('theme_id', $themeId, \PDO::PARAM_INT);
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function update(int $subjectId, string $name): int
    {
        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET name = :name WHERE id = :id");

        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('id', (int)$subjectId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }
}
