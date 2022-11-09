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
    public function index(string $notionId): string
    {

        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        //Récuperer l'id du sujet de la notion
        $notionManager = new NotionManager();
        $subjectId = $notionManager->getSubjectId((int)$notionId);

        //récuperer toutes les notions du sujet
        $notions = $notionManager->selectAllBySubject($subjectId);

        $themeManager = new ThemeManager();

        //Récuperer le thème à partir du sujet
        $theme = $themeManager->selectThemeBySubject((int)$subjectId);

        $subjectManager = new SubjectManager();

        //récuperer tous les sujets à partir du theme
        $subjects = $subjectManager->selectAllByTheme((int)$theme['id']);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headertitle' => $theme['name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'notion' => $notionManager->selectOneById((int)$notionId),
                'idsubject' => $subjectId
            ]
        );
    }
}
