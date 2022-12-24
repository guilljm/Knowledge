<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;

class ThemeController extends AbstractController
{
    public const TITLE = 'KNOWLEDGE';
    private ThemeManager $themeManager;
    private SubjectManager $subjectManager;

    public function __construct()
    {
        $this->themeManager = new ThemeManager();
        $this->subjectManager = new SubjectManager();
        parent::__construct();
    }
    
    public function show(string $themeId): string
    {
        if (!is_numeric($themeId)) {
            header("Location: /");
        }

        $theme = $this->themeManager->selectOneById((int)$themeId);

        $_SESSION['theme_id'] = $themeId;
        $_SESSION['theme_name'] = $theme['name'];

        $subjects = $this->subjectManager->selectAllByTheme((int)$themeId);

        return $this->twig->render(
            'Theme/index.html.twig',
            [
                'headerTitle' => $theme['name'],
                'subjects' => $subjects
            ]
        );
    }
}
