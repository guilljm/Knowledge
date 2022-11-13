<?php

namespace App\Controller;

class ExerciseController extends AbstractController
{
    /**
     * Display home page
     */
    public function add(): string
    {
        return $this->twig->render(
            'Exercise/index.html.twig',
            []
        );
    }
}
