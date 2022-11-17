<?php

namespace App\Model;

use PDO;

class NotionManager extends AbstractManager
{
    public const TABLE = 'notion';

    public function selectAllBySubject(int $subjectId): array
    {

        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE `subject_id` = :subject_id");
        $statement->bindValue('subject_id', $subjectId, \PDO::PARAM_INT);
        $statement->execute();


        return $statement->fetchAll();
    }


    public function getName(string $name, int $subjectId): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name = :name and subject_id = :id");
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('id', $subjectId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }


    public function add(
        int $subjectId,
        string $notionName,
        string $lesson,
        string $sample,
        string $fileNameImg
    ): int {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (subject_id, name, lesson, sample, image) 
            VALUES (:subject_id, :notion_name, :lesson, :sample, :image)");

        $statement->bindValue('subject_id', $subjectId, \PDO::PARAM_INT);
        $statement->bindValue('notion_name', $notionName, \PDO::PARAM_STR);
        $statement->bindValue('lesson', $lesson, \PDO::PARAM_STR);
        $statement->bindValue('sample', $sample, \PDO::PARAM_STR);
        $statement->bindValue('image', $fileNameImg, \PDO::PARAM_STR);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function update(
        int $notionId,
        int $subjectId,
        string $notionName,
        string $lesson,
        string $sample,
        string $fileNameImg
    ): int {

        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET subject_id = :subject_id, 
            name = :notion_name, 
            lesson = :lesson, 
            sample = :sample, 
            image = :image 
            WHERE id = :notion_id");

        $statement->bindValue('subject_id', $subjectId, \PDO::PARAM_INT);
        $statement->bindValue('notion_name', $notionName, \PDO::PARAM_STR);
        $statement->bindValue('lesson', $lesson, \PDO::PARAM_STR);
        $statement->bindValue('sample', $sample, \PDO::PARAM_STR);
        $statement->bindValue('image', $fileNameImg, \PDO::PARAM_STR);
        $statement->bindValue('notion_id', $notionId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }
}
