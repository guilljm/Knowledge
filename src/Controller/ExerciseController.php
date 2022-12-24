<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\NotionManager;

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

                if (!empty($this->exerciseManager->isExist($exercise['name']))) {
                    $errors['name'] = "Exercice déjà existant";
                }

                if (!empty($errors)) {
                    return $this->twig->render(
                        'Exercise/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter un nouvel exercice à ' . $notion['name'],
                            'notion' => $notion,
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
                'notion' => $notion
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

                    $exerciseForm = array_map("trim", $_POST);

                    if ($exerciseForm['name'] == "") {
                        $errors['name'] = "Veuillez saisir le nom de l'exercice";
                    }

                    if (!filter_var($exerciseForm['url'], FILTER_VALIDATE_URL)) {
                        $errors['url'] = "Le format de l'url est incorrect";
                    }

                    if ($exerciseForm['url']  == "") {
                        $errors['url'] = "Veuillez saisir le nom de l'url";
                    }

                    if ($exerciseForm['name'] != $exercise['name']) {
                        if (!empty($this->exerciseManager->isExist($exerciseForm['name']))) {
                            $errors['name'] = "Exercice déjà existante";
                        }
                    }

                    if (!empty($errors)) {
                        return $this->twig->render(
                            'Exercise/add.html.twig',
                            [
                                'headerTitle' => $_SESSION['theme_name'],
                                'titleForm' => 'Modifier l\'exercice de ' .  $notion['name'],
                                'notion' => $notion,
                                'errors' => $errors,
                                'exercise' => $exercise
                            ]
                        );
                    }

                    $this->exerciseManager->update($exerciseId, $exerciseForm);

                    header("Location: /notion/show?id=" . $notion['id']);
                }
            }
        }

        return $this->twig->render(
            'Exercise/edit.html.twig',
            [
                'exercise' => $exercise,
                'titleForm' => 'Modifier l\'exercice de ' . $notion['name'],
                'notion' => $notion
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
