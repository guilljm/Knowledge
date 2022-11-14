<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\SubjectManager;
use App\Model\ThemeManager;
use App\Model\NotionManager;

class NotionController extends AbstractController
{
    /**
     * Display home page
     */
    public function show(string $notionId): string
    {
        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            // var_dump($_SESSION);
            // exit();
            return "Session variables undefined";
        }

        //Récuperer l'id du sujet de la notion
        $notionManager = new NotionManager();
        $subjectId = $notionManager->getSubjectId((int)$notionId);

        //récuperer toutes les notions du sujet
        $notions = $notionManager->selectAllBySubject($subjectId);

        $subjectManager = new SubjectManager();

        //récuperer tous les sujets à partir du theme
        $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);

        // récupérer tous les exercices d'un notion
        $exerciseManager = new ExerciseManager();

        $exercises = $exerciseManager->selectAllByNotion($notionId);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'notion' => $notionManager->selectOneById((int)$notionId),
                'exercises' => $exercises,
                'idsubject' => $subjectId
            ]
        );
    }


    public function delete(string $notionId): void
    {

        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        $notionManager = new NotionManager();
        $subjectId = $notionManager->getSubjectId((int)$notionId);

        $notionManager->delete((int)$notionId);

        header("Location: /subject/show?id=" . $subjectId);
    }

    public function add(string $subjectId): string
    {

        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['button'])) {
                if ($_POST['button'] == "Annuler") {

                    header("Location: /subject/show?id=" . $subjectId);
                    return "";
                }
            }

            $fileNameImg = "";

            //button Valider
            if (isset($_FILES['filename']) && $_FILES['filename']['name'] != "") {
                $uploadDir = '../upload/';
                $fileNameImg = $uploadDir . basename($_FILES['filename']['name']);
                $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
                $authorizedExtensions = ['jpg', 'jpeg', 'png'];
                $maxFileSize = 1000000;
                $errors = [];

                if ((!in_array($extension, $authorizedExtensions))) {
                    $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
                }

                if (file_exists($_FILES['filename']['tmp_name']) && filesize($_FILES['filename']['tmp_name']) > $maxFileSize) {
                    $errors[] = "Votre fichier doit faire moins de 1M !";
                }

                if (!empty($errors)) {
                    return $errors[0];
                }
            }

            $notionName = $_POST['notion'];
            $lesson = $_POST['lesson'];
            $sample = $_POST['sample'];

            $notionManager = new NotionManager();
            $notionManager->insert((int)$subjectId, $notionName, $lesson, $sample, $fileNameImg);
            header("Location: /subject/show?id=" . $subjectId);

            return "";
        }

        return $this->twig->render(
            'Notion/add_update.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
            ]
        );
    }


    public function update(string $notionId): string
    {

        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $notionManager = new NotionManager();
        $subjectId = $notionManager->getSubjectId((int)$notionId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['button'])) {
                if ($_POST['button'] == "Annuler") {
                    header("Location: /subject/show?id=" . $subjectId);
                    return "";
                }

                if ($_POST['button'] == "Valider") {
                    $notionName = $_POST['notion'];
                    $lesson = $_POST['lesson'];
                    $sample = $_POST['sample'];
                    $fileNameImg = "";

                    // var_dump($_FILES);
                    // exit();
                    if (isset($_FILES['filename']) && $_FILES['filename']['name'] != "") {
                        $uploadDir = '../upload/';
                        $fileNameImg = $uploadDir . basename($_FILES['filename']['name']);
                        $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
                        $authorizedExtensions = ['jpg', 'jpeg', 'png'];
                        $maxFileSize = 1000000;
                        $errors = [];

                        if ((!in_array($extension, $authorizedExtensions))) {
                            $errors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
                        }

                        if (file_exists($_FILES['filename']['tmp_name']) && filesize($_FILES['filename']['tmp_name']) > $maxFileSize) {
                            $errors[] = "Votre fichier doit faire moins de 1M !";
                        }

                        if (!empty($errors)) {
                            return $errors[0];
                        }
                    }

                    $notionManager->update(
                        (int)$notionId,
                        (int)$subjectId,
                        $notionName,
                        $lesson,
                        $sample,
                        $fileNameImg
                    );

                    header("Location: /subject/show?id=" . $subjectId);
                }
            }
        }


        $notion = $notionManager->selectOneById((int) $notionId);
        $notionName = $notion['name'];
        $lesson = $notion['lesson'];
        $sample = $notion['sample'];

        return $this->twig->render(
            'Notion/add_update.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'notionName' => $notionName,
                'lesson' => $lesson,
                'sample' => $sample
            ]
        );
    }
}
