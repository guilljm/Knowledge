<?php

namespace App\Model;

use PDO;

class ThemeManager extends AbstractManager
{
    public const TABLE = 'theme';

    public function getThemeName(int $subjectId): string
    {

        $statement = $this->pdo->prepare("SELECT name FROM " . self::TABLE .
            " WHERE id IN (select theme_id FROM subject WHERE id=:id)");
        $statement->bindValue('id', $subjectId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch()['name'];
    }

    public function selectThemeBySubject(int $subjectId): array
    {

        $statement = $this->pdo->prepare("SELECT * FROM " . self::TABLE .
            " WHERE id IN (select theme_id FROM subject WHERE id=:id)");
        $statement->bindValue('id', $subjectId, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch();
    }
}
