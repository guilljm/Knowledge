<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\NotionManager;

class ExerciseController extends AbstractController
{
    private NotionManager $notionManager;
    private ExerciseManager $exerciseManager;

    /**
     * Display home page
     */
    public function add(string $notionId): string
    {

        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $this->notionManager = new NotionManager();
        $notion = $this->notionManager->selectOneById($notionId);

        if (!$notion) {
            header("Location: /");
        }

        // $subjectId = $this->notionManager->selectOneById((int)$notionId)['subject_id'];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {
                $nameErrors = [];
                $urlErrors = [];

                $name = trim($_POST['name']);
                $url = trim($_POST['url']);

                if ($name == "") {
                    $nameErrors[] = "Veuillez saisir le champ";
                }

                if ($url == "") {
                    $urlErrors[] = "Veuillez saisir le champ";
                }

                if ($name == "" || $url == "") {

                    return $this->twig->render(
                        'Exercise/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter un nouvel exercice à ' . $notion['name'],
                            'notionId' => $notionId,
                            'nameErrors' => $nameErrors,
                            'urlErrors' => $urlErrors
                        ]
                    );
                }

                $this->exerciseManager = new ExerciseManager();
                $this->exerciseManager->add($notionId, $name, $url);

                // $validationMessage = 'Bravo ! le nouvel exercise ' . $name .  ' a bien été ajouté.';

                // return $this->twig->render(
                //     'Exercise/add.html.twig',
                //     [
                //         'headerTitle' => $_SESSION['theme_name'],
                //         'titleForm' => 'Ajouter un nouvel exercice',
                //         'validationMessage' => $validationMessage,
                //         'subjectId' => $subjectId
                //     ]
                // );

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
            return "Session variables undefined";
        }

        $exerciseManager = new ExerciseManager();
        $exercise = $exerciseManager->selectOneById($exerciseId);

        if (!$exercise) {
            header("Location: /");
        }

        $notionManager = new NotionManager();
        // $subjectId = $notionManager->selectOneById((int)$_SESSION['theme_id'])['subject_id'];
        $notion = $notionManager->selectOneById($exercise['notion_id']);


        // $validationMessage = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            if (isset($_POST['button'])) {

                if ($_POST['button'] == "Valider") {

                    $urlErrors = [];
                    $nameErrors = [];

                    $name = trim($_POST['name']);
                    $url = trim($_POST['url']);

                    if ($name == "") {
                        $nameErrors[] = "Veuillez saisir le champ";
                    }

                    if ($url == "") {
                        $urlErrors[] = "Veuillez saisir le champ";
                    }

                    if ($name == "" || $url == "") {

                        return $this->twig->render(
                            'Exercise/add.html.twig',
                            [
                                'headerTitle' => $_SESSION['theme_name'],
                                'titleForm' => 'Modifier l\'exercice de ' .  $notion['name'],
                                'notiontId' => $notion['id'],
                                'nameErrors' => $nameErrors,
                                'urlErrors' => $urlErrors
                            ]
                        );
                    }

                    $exerciseManager->update($exerciseId, $_POST['name'], $_POST['url']);

                    header("Location: /notion/show?id=" . $notion['id']);

                    // $validationMessage = 'Bravo ! le nouvel exercise ' . $name .  ' a bien été modifié.';
                }
            }
        }

        return $this->twig->render(
            'Exercise/edit.html.twig',
            [
                'name' => $exercise['name'],
                'url' => $exercise['url'],
                'titleForm' => 'Modifier l\'exercice de ' . $notion['name'],
                'notionId' => $notion['id']
                // 'validationMessage' => $validationMessage,
            ]
        );
    }


    public function delete(string $exerciseId): void
    {

        if (!is_numeric($exerciseId)) {
            header("Location: /");
        }

        $exerciseManager = new ExerciseManager();
        $exercise = $exerciseManager->selectOneById((int)$exerciseId);
        $exerciseManager->delete((int)$exerciseId);

        $notionManager = new NotionManager();

        $notion = $notionManager->selectOneById((int)$exercise['notion_id']);

        header("Location: /subject/show?id=" . $notion['subject_id']);
    }
}
