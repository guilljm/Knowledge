<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\SubjectManager;
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
        $subjectId = $notionManager->selectOneById((int)$notionId)['subject_id'];

        //récuperer toutes les notions du sujet
        $notions = $notionManager->selectAllBySubject($subjectId);

        //récuperer le sujet et tous les sujets à partir du theme
        $subjectManager = new SubjectManager();
        $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);
        $subject = $subjectManager->selectOneById((int)$subjectId);

        // récupérer tous les exercices d'un notion
        $exerciseManager = new ExerciseManager();
        $exercises = $exerciseManager->selectAllByNotion($notionId);

        if (isset($_POST['id']) && isset($_POST['delete'])) {
            $notionManager->delete((int)$notionId);
            header("Location: /subject/show?id=" . $subjectId);

            // var_dump($_POST);
            // exit();
        }

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'subjectname' => $subject['name'],
                'notions' => $notions,
                'notion' => $notionManager->selectOneById((int)$notionId),
                'exercises' => $exercises,
                'subjectId' => $subjectId
            ]
        );
    }


    // public function delete(string $notionId): string
    // {

    //     if (!is_numeric($notionId)) {
    //         header("Location: /");
    //     }

    //     if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
    //         // var_dump($_SESSION);
    //         // exit();
    //         return "Session variables undefined";
    //     }

    //     $notionManager = new NotionManager();
    //     // $subjectId = $notionManager->selectOneById((int)$notionId)['subject_id'];

    //     $notion = $notionManager->selectOneById($notionId);

    //     if (!$notion) {

    //         // $subjectManager = new SubjectManager();
    //         // $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);

    //         // if ($subjects) {
    //         //     header("Location: /subject/show?id=" . $subjects[0]['id']);
    //         // }
    //         header("Location: /");
    //         return "";
    //         // return $this->twig->render(
    //         //     'Notion/index.html.twig',
    //         //     [
    //         //         'headerTitle' => $_SESSION['theme_name'],
    //         //         'subjects' => $subjects
    //         //     ]
    //         // );
    //     }

    //     $subjectId = $notionManager->selectOneById((int)$notionId)['subject_id'];

    //     $notionManager->delete((int)$notionId);

    //     $notions = $notionManager->selectAllBySubject($subjectId);

    //     $subjectManager = new SubjectManager();
    //     $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);

    //     $subject = $subjectManager->selectOneById((int)$subjectId);

    //     return $this->twig->render(
    //         'Notion/index.html.twig',
    //         [
    //             'headerTitle' => $_SESSION['theme_name'],
    //             'subjects' => $subjects,
    //             'notions' => $notions,
    //             'subjectname' => $subject['name'],
    //             'subjectId' => $subjectId,
    //             'validationMessage' => 'La notion ' . $notion['name'] . ' a bien été détruite'
    //         ]
    //     );
    // }

    public function add(string $subjectId): string
    {

        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $subjectManager = new SubjectManager();
        $subject = $subjectManager->selectOneById((int)$subjectId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

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
                        return $this->twig->render(
                            'Notion/add.html.twig',
                            [
                                'headerTitle' => $_SESSION['theme_name'],
                                'titleForm' => 'Ajouter une nouvelle notion à ' . $subject['name'],
                                'subjectId' => $subjectId,
                                'fileErrors' => $errors
                            ]
                        );
                    }
                }

                $notionName = trim($_POST['notion']);

                if ($notionName == "") {
                    $errors[] = "Veuillez saisir le champ";

                    return $this->twig->render(
                        'Notion/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter une nouvelle notion à ' . $subject['name'],
                            'subjectId' => $subjectId,
                            'nameErrors' => $errors
                        ]
                    );
                }

                $lesson = trim($_POST['lesson']);
                $sample = trim($_POST['sample']);

                $notionManager = new NotionManager();

                if (($notionManager->getName($notionName, $subjectId))) {
                    $errors[] = "Notion déjà existante";

                    return $this->twig->render(
                        'Notion/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter une nouvelle notion à ' . $subject['name'],
                            'subjectId' => $subjectId,
                            'nameErrors' => $errors
                        ]
                    );
                }

                $newNotionId = $notionManager->add((int)$subjectId, $notionName, $lesson, $sample, $fileNameImg);

                // return $this->twig->render(
                //     'Notion/add.html.twig',
                //     [
                //         'headerTitle' => $_SESSION['theme_name'],
                //         'titleForm' => 'Ajouter une nouvelle notion',
                //         // 'validationMessage' => 'Bravo ! la nouvelle notion ' . $notionName .  ' a bien été ajoutée.',
                //         'notionId' => $newNotionId,
                //         'subjectId' => $subjectId
                //     ]
                // );

                header("Location: /exercise/add?notionid=" . $newNotionId);
                return "";
            }
        }

        return $this->twig->render(
            'Notion/add.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Ajouter une nouvelle notion à ' . $subject['name'],
                'subjectId' => $subjectId
            ]
        );
    }

    public function edit(string $notionId): string
    {
        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $notionManager = new NotionManager();
        $notion = $notionManager->selectOneById((int)$notionId);

        if (!$notion) {
            header("Location: /");
        }

        $subjectManager = new SubjectManager();
        $subject = $subjectManager->selectOneById((int)$notion['subject_id']);

        if (!$subject) {
            header("Location: /");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['button'])) {
                if ($_POST['button'] == "Annuler") {
                    header("Location: /subject/show?id=" . $subject['id']);
                    return "";
                }

                if ($_POST['button'] == "Valider") {
                    $notionName = trim($_POST['notion']);
                    $lesson = trim($_POST['lesson']);
                    $sample = trim($_POST['sample']);
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
                            return $this->twig->render(
                                'Notion/add.html.twig',
                                [
                                    'headerTitle' => $_SESSION['theme_name'],
                                    'titleForm' => 'Modifier la notion de ' . $subject['name'],
                                    // 'subjectId' => $subject['id'],
                                    'notionId' => $notionId,
                                    'fileErrors' => $errors
                                ]
                            );
                        }
                    }

                    if ($notionName == "") {
                        $errors[] = "Veuillez compléter le champ";

                        return $this->twig->render(
                            'Notion/add.html.twig',
                            [
                                'headerTitle' => $_SESSION['theme_name'],
                                'titleForm' => 'Modifier la notion de ' . $subject['name'],
                                'notionId' => $notionId,
                                'nameErrors' => $errors
                            ]
                        );
                    }

                    $notionManager->update(
                        (int)$notionId,
                        (int)$subject['id'],
                        $notionName,
                        $lesson,
                        $sample,
                        $fileNameImg
                    );

                    // $validationMessage = 'Bravo ! la notion ' . $notionName .  ' a bien été modifiée.';

                    header("Location: /notion/show?id=" . $notionId);
                }
            }
        }

        $notion = $notionManager->selectOneById((int) $notionId);
        $notionName = $notion['name'];
        $lesson = $notion['lesson'];
        $sample = $notion['sample'];

        return $this->twig->render(
            'Notion/edit.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'notionName' => $notionName,
                'lesson' => $lesson,
                'sample' => $sample,
                'titleForm' => 'Modifier la notion de ' . $subject['name'],
                // 'validationMessage' => $validationMessage,
                'notionId' => $notionId
                // 'subjectId' => $subjectId
            ]
        );
    }
}
