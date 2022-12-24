<?php

namespace App\Controller;

use App\Model\ExerciseManager;
use App\Model\SubjectManager;
use App\Model\NotionManager;

class NotionController extends AbstractController
{
    private SubjectManager $subjectManager;
    private NotionManager $notionManager;

    public function __construct()
    {
        $this->subjectManager = new SubjectManager();
        $this->notionManager = new NotionManager();
        parent::__construct();
    }

    /**
     * Display home page
     */
    public function show(string $notionId): string
    {
        if (!is_numeric($notionId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $subjectId = $this->notionManager->selectOneById((int)$notionId)['subject_id'];
        $notions = $this->notionManager->selectAllBySubject($subjectId);

        $subjects = $this->subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);
        $subject = $this->subjectManager->selectOneById((int)$subjectId);

        $exerciseManager = new ExerciseManager();
        $exercises = $exerciseManager->selectAllByNotion($notionId);

        if (isset($_POST['id']) && isset($_POST['delete'])) {
            $this->notionManager->delete((int)$notionId);
            header("Location: /subject/show?id=" . $subjectId);
        }

        return $this->twig->render(
            'Theme/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'subjectname' => $subject['name'],
                'notions' => $notions,
                'notion' => $this->notionManager->selectOneById((int)$notionId),
                'exercises' => $exercises,
                'subjectId' => $subjectId
            ]
        );
    }

    public function add(string $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $subject = $this->subjectManager->selectOneById((int)$subjectId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {


            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

                $fileNameImg = "";
                $fileErrors = [];

                if (isset($_FILES['filename']) && $_FILES['filename']['name'] != "") {
                    $uploadDir = '../upload/';
                    $fileNameImg = $uploadDir . basename($_FILES['filename']['name']);
                    $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
                    $authorizedExtensions = ['jpg', 'jpeg', 'png'];
                    $maxFileSize = 1000000;

                    if ((!in_array($extension, $authorizedExtensions))) {
                        $fileErrors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
                    }

                    if (file_exists($_FILES['filename']['tmp_name']) && filesize($_FILES['filename']['tmp_name']) > $maxFileSize) {
                        $fileErrors[] = "Votre fichier doit faire moins de 1M !";
                    }
                }

                $notion = array_map("trim", $_POST);

                if (empty($notion['notion'])) {
                    $errors[] = "Veuillez saisir le champ";
                }

                if (($this->notionManager->isNotion($notion['notion'], $subjectId))) {
                    $errors[] = "Notion déjà existante";
                }

                if (!empty($errors)) {
                    return $this->twig->render(
                        'Notion/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter une nouvelle notion à ' . $subject['name'],
                            'subjectId' => $subjectId,
                            'fileErrors' => $fileErrors,
                            'errors' => $errors
                        ]
                    );
                }

                $notion['fileNameImg'] = $fileNameImg;
                $newNotionId = $this->notionManager->insert((int)$subjectId, $notion);

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
            header("Location: /");
        }

        $notion = $this->notionManager->selectOneById((int)$notionId);

        if (!$notion) {
            header("Location: /");
        }

        $subject = $this->subjectManager->selectOneById((int)$notion['subject_id']);

        if (!$subject) {
            header("Location: /");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

                $notionForm = array_map("trim", $_POST);
                $fileNameImg = "";
                $errors = [];
                $fileErrors = [];

                if (isset($_FILES['filename']) && $_FILES['filename']['name'] != "") {
                    $uploadDir = '../upload/';
                    $fileNameImg = $uploadDir . basename($_FILES['filename']['name']);
                    $extension = pathinfo($_FILES['filename']['name'], PATHINFO_EXTENSION);
                    $authorizedExtensions = ['jpg', 'jpeg', 'png'];
                    $maxFileSize = 1000000;

                    if ((!in_array($extension, $authorizedExtensions))) {
                        $fileErrors[] = 'Veuillez sélectionner une image de type Jpg ou Jpeg ou Png !';
                    }

                    if (file_exists($_FILES['filename']['tmp_name']) && filesize($_FILES['filename']['tmp_name']) > $maxFileSize) {
                        $fileErrors[] = "Votre fichier doit faire moins de 1M !";
                    }
                }

                if (empty($notionForm['notion'])) {
                    $errors[] = "Veuillez compléter le champ";
                }

                if ($notionForm['notion'] != $notion['name']) {
                    if (($this->notionManager->isNotion($notionForm['notion'], (int)$notion['subject_id']))) {
                        $errors[] = "Notion déjà existante";
                    }
                }

                if (!empty($errors)) {
                    return $this->twig->render(
                        'Notion/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Modifier la notion de ' . $subject['name'],
                            'notionName' => $notionForm['notion'],
                            'lesson' => $notionForm['lesson'],
                            'sample' => $notionForm['sample'],
                            'notionId' => $notionId,
                            'errors' => $errors,
                            'fileErrors' => $fileErrors
                        ]
                    );
                }

                $notionForm['fileNameImg'] = $fileNameImg;
                $this->notionManager->update((int)$notionId, (int)$subject['id'], $notionForm);

                header("Location: /notion/show?id=" . $notionId);
            }
        }

        return $this->twig->render(
            'Notion/edit.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'notionName' => $notion['name'],
                'lesson' => $notion['lesson'],
                'sample' => $notion['sample'],
                'titleForm' => 'Modifier la notion de ' . $subject['name'],
                'notionId' => $notionId
            ]
        );
    }

    public function delete()
    {
        if (isset($_POST['response'])) {
            $notionId = (int)$_POST['response'];

            $subjectId = $this->notionManager->selectOneById($notionId)['subject_id'];

            $this->notionManager->delete($notionId);

            $route = '/subject/show?id=' . $subjectId;

            return ($route);
        }
    }
}
