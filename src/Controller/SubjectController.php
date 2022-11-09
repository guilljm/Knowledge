<?php

namespace App\Controller;

use App\Model\NotionManager;
use App\Model\SubjectManager;
use App\Model\ThemeManager;

class SubjectController extends AbstractController
{
    /**
     * Display Subject List, Notion List & select active subject
     */
    public function index(string $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        $themeManager = new ThemeManager();

        //Récuperer le thème à partir du sujet
        $theme = $themeManager->selectThemeBySubject((int)$subjectId);

        //Récuperer tous les sujets du thème
        $subjectManager = new SubjectManager();
        $subjects = $subjectManager->selectAllByTheme((int)$theme['id']);

        //Récuperer toutes les notions du sujet
        $notionManager = new NotionManager();
        $notions = $notionManager->selectAllBySubject((int)$subjectId);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headertitle' => $theme['name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'idsubject' => $subjectId
            ]
        );
    }
}
