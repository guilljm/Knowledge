<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;
use App\Model\NotionManager;

class ThemeController extends AbstractController
{
    public const TITLE = 'KNOWLEDGE';
    /**
     * Display ThemeList
     */

    public function index(): string
    {
        $themeManager = new ThemeManager();

        return $this->twig->render(
            'Theme/index.html.twig',
            [
                'headerTitle' => self::TITLE,
                'themes' => $themeManager->selectAll()
            ]
        );
    }

    public function show(string $themeId): string
    {
        if (!is_numeric($themeId)) {
            header("Location: /");
        }

        $_SESSION['theme_id'] = $themeId;

        $themeManager = new ThemeManager();

        //Récuperer le thème à partir du sujet
        $theme = $themeManager->selectOneById((int)$themeId);

        $_SESSION['theme_name'] = $theme['name'];

        //Récuperer tous les sujets du thème
        $subjectManager = new SubjectManager();
        $subjects = $subjectManager->selectAllByTheme((int)$themeId);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $theme['name'],
                'subjects' => $subjects
            ]
        );
    }
}
