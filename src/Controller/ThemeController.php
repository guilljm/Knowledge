<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;
use App\Model\NotionManager;

class ThemeController extends AbstractController
{
    /**
     * Display Subject List, Notion List & select first element
     */
    public function index(string $themeId): string
    {

        $subjectManager = new SubjectManager();
        $subjects = $subjectManager->selectAllByTheme((int)$themeId);

        $themeManager = new ThemeManager();

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headertitle' => $themeManager->selectOneById((int)$themeId)['name'],
                'subjects' => $subjects
            ]
        );
    }
}
