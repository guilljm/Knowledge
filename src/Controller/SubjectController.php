<?php

namespace App\Controller;

use App\Model\NotionManager;
use App\Model\SubjectManager;
use App\Model\ThemeManager;

class SubjectController extends AbstractController
{
    public function show(string $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {

            return "Session variables undefined";
        }


        //Récuperer tous les sujets du thème
        $subjectManager = new SubjectManager();
        // $subjects = $subjectManager->selectAllByTheme((int)$theme['id']);
        $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);

        //Récuperer toutes les notions du sujet
        $notionManager = new NotionManager();
        $notions = $notionManager->selectAllBySubject((int)$subjectId);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'idsubject' => $subjectId
            ]
        );
    }
}
