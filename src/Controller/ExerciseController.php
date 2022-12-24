<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\NotionManager;
use Error;

class ExerciseController extends AbstractController
{
    private NotionManager $notionManager;
    private ExerciseManager $exerciseManager;

    public function __construct()
    {
        $this->exerciseManager = new ExerciseManager();
        $this->notionManager = new NotionManager();
        parent::__construct();
    }

    /**
     * Display home page
     */
    public function add(string $notionId): string
    {

        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $notion = $this->notionManager->selectOneById($notionId);

        if (!$notion) {
            header("Location: /");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {
                $errors = [];

                $exercise = array_map("trim", $_POST);

                if ($exercise['name'] == "") {
                    $errors['name'] = "Veuillez saisir le nom de l'exercice";
                }

                if (!filter_var($exercise['url'], FILTER_VALIDATE_URL)) {
                    $errors['url'] = "Le format de l'url est incorrect";
                }

                if ($exercise['url']  == "") {
                    $errors['url'] = "Veuillez saisir le nom de l'url";
                }


                if (!empty($errors)) {

                    return $this->twig->render(
                        'Exercise/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter un nouvel exercice à ' . $notion['name'],
                            'notionId' => $notionId,
                            'errors' => $errors,
                            'exercise' => $exercise
                        ]
                    );
                }

                $this->exerciseManager->insert($notionId, $exercise);

                header("Location: /notion/show?id=" . $notionId);
            }
        }

        return $this->twig->render(
            'Exercise/add.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Ajouter un nouvel exercice à ' . $notion['name'],
                'notionId' => $notionId
            ]
        );
    }

    public function edit(string $exerciseId): string
    {
        if (!is_numeric($exerciseId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $exercise = $this->exerciseManager->selectOneById($exerciseId);

        if (!$exercise) {
            header("Location: /");
        }

        $notion = $this->notionManager->selectOneById($exercise['notion_id']);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button'])) {

                if ($_POST['button'] == "Valider") {
                    $errors = [];

                    $exercise = array_map("trim", $_POST);

                    if ($exercise['name'] == "") {
                        $errors['name'] = "Veuillez saisir le nom de l'exercice";
                    }

                    if (!filter_var($exercise['url'], FILTER_VALIDATE_URL)) {
                        $errors['url'] = "Le format de l'url est incorrect";
                    }

                    if ($exercise['url']  == "") {
                        $errors['url'] = "Veuillez saisir le nom de l'url";
                    }

                    if (!empty($errors)) {

                        return $this->twig->render(
                            'Exercise/add.html.twig',
                            [
                                'headerTitle' => $_SESSION['theme_name'],
                                'titleForm' => 'Modifier l\'exercice de ' .  $notion['name'],
                                'notionId' => $notion['id'],
                                'errors' => $errors,
                                'exercise' => $exercise
                            ]
                        );
                    }

                    $this->exerciseManager->update($exerciseId, $exercise);

                    header("Location: /notion/show?id=" . $notion['id']);

                }
            }
        }

        return $this->twig->render(
            'Exercise/edit.html.twig',
            [
                'exercise' => $exercise,
                'titleForm' => 'Modifier l\'exercice de ' . $notion['name'],
                'notionId' => $notion['id']
            ]
        );
    }

    public function delete()
    {
        if (isset($_POST['response'])) {

            $exerciseId = (int)$_POST['response'];

            $exercise = $this->exerciseManager->selectOneById($exerciseId);

            $notionId = (int)$exercise['notion_id'];

            $this->exerciseManager->delete($exerciseId);

            $route = '/notion/show?id=' . $notionId;

            return ($route);
        }
    }
}
