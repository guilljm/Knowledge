<?php

namespace App\Controller;

use App\Model\ThemeManager;

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
