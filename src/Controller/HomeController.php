<?php

namespace App\Controller;

use App\Model\SubjectManager;
use App\Model\ThemeManager;

class HomeController extends AbstractController
{
    /**
     * Display Subject List, Notion List & select first element
     */
    public function index(): string
    {

        $themeManager = new ThemeManager();

        return $this->twig->render(
            'Theme/index.html.twig',
            [
                'headertitle' => 'KNOWLEDGE',
                'themes' => $themeManager->selectAll()
            ]
        );
    }
}
