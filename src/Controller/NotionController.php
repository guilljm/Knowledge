<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;
use App\Model\NotionManager;

class NotionController extends AbstractController
{
    /**
     * Display home page
     */
    public function show(string $notionId): string
    {
        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            var_dump($_SESSION);
            exit();
            return "Session variables undefined";
        }

        //Récuperer l'id du sujet de la notion
        $notionManager = new NotionManager();
        $subjectId = $notionManager->getSubjectId((int)$notionId);

        //récuperer toutes les notions du sujet
        $notions = $notionManager->selectAllBySubject($subjectId);

        $subjectManager = new SubjectManager();

        //récuperer tous les sujets à partir du theme
        $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'notion' => $notionManager->selectOneById((int)$notionId),
                'idsubject' => $subjectId
            ]
        );
    }

    public function add(): string
    {
        return $this->twig->render(
            'Notion/add.html.twig',
            []
        );
    }
}
