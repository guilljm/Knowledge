<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\NotionManager;

class ExerciseController extends AbstractController
{
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $notionManager = new NotionManager();
            $subjectId = $notionManager->selectOneById((int)$notionId)['subject_id'];


            // if (isset($_POST['button'])) {
            //     if ($_POST['button'] == "Annuler") {

            //         header("Location: /subject/show?id=" . $subjectId);
            //         return "";
            //     }
            // }
            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

                $name = trim($_POST['name']);
                $url = trim($_POST['url']);

                if ($name == "") {
                    $NameErrors[] = "Veuillez saisir le champ";
                }

                if ($url == "") {
                    $UrlErrors[] = "Veuillez saisir le champ";
                }

                $exerciseManager = new ExerciseManager();
                $exerciseManager->add($notionId, $name, $url);

                header("Location: /subject/show?id=" . $subjectId);
            }
        }

        return $this->twig->render(
            'Exercise/add.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Ajouter un nouvel exercice'
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['button'])) {

                $notionManager = new NotionManager();
                $notion = $notionManager->selectOneById((int)$exercise['notion_id']);

                if ($_POST['button'] == "Annuler") {
                    header("Location: /subject/show?id=" . $notion['subject_id']);
                    return "";
                }

                if ($_POST['button'] == "Valider") {
                    $exerciseManager->update($exerciseId, $_POST['name'], $_POST['url']);
                    header("Location: /subject/show?id=" . $notion['subject_id']);
                }
            }
        }


        return $this->twig->render(
            'Exercise/edit.html.twig',
            [
                'name' => $exercise['name'],
                'url' => $exercise['url'],
                'titleForm' => 'Modifier cette exercice'
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
