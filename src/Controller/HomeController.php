<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;
use App\Model\NotionManager;

class HomeController extends AbstractController
{
    public const TITLE = 'KNOWLEDGE';

    public function index(): string
    {
        $themeManager = new ThemeManager();

        return $this->twig->render(
            'Home/index.html.twig',
            [
                'headerTitle' => self::TITLE,
                'themes' => $themeManager->selectAll()
            ]
        );
    }
}
