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

    public function IsNotion(string $name, int $subjectId): array|false
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE . " WHERE name = :name and subject_id = :id");
        $statement->bindValue('name', $name, \PDO::PARAM_STR);
        $statement->bindValue('id', $subjectId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }

    public function insert(int $subjectId, array $notion): int {

        $statement = $this->pdo->prepare("INSERT INTO " . self::TABLE . " 
            (subject_id, name, lesson, sample, file_image) 
            VALUES (:subject_id, :notion_name, :lesson, :sample, :file_image)");

        $statement->bindValue('subject_id', $subjectId, \PDO::PARAM_INT);
        $statement->bindValue('notion_name', $notion['notion'], \PDO::PARAM_STR);
        $statement->bindValue('lesson', $notion['lesson'], \PDO::PARAM_STR);
        $statement->bindValue('sample', $notion['sample'], \PDO::PARAM_STR);
        $statement->bindValue('file_image', $notion['fileNameImg'], \PDO::PARAM_STR);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }

    public function update(int $notionId, int $subjectId, array $notion): int {

        $statement = $this->pdo->prepare("UPDATE " . self::TABLE . " 
            SET subject_id = :subject_id, 
            name = :notion_name, 
            lesson = :lesson, 
            sample = :sample, 
            file_image = :file_image 
            WHERE id = :notion_id");

        $statement->bindValue('subject_id', $subjectId, \PDO::PARAM_INT);
        $statement->bindValue('notion_name', $notion['notion'], \PDO::PARAM_STR);
        $statement->bindValue('lesson', $notion['lesson'], \PDO::PARAM_STR);
        $statement->bindValue('sample', $notion['sample'], \PDO::PARAM_STR);
        $statement->bindValue('file_image', $notion['fileNameImg'], \PDO::PARAM_STR);
        $statement->bindValue('notion_id', $notionId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->pdo->lastInsertId();
    }
}
