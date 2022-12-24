<?php

namespace App\Controller;

use App\Model\NotionManager;
use App\Model\SubjectManager;


class SubjectController extends AbstractController
{
    private SubjectManager $subjectManager;

    public function __construct()
    {
        $this->subjectManager = new SubjectManager();
        parent::__construct();
    }

    public function show(string $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $subjects = $this->subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);
        $subject = $this->subjectManager->selectOneById($subjectId);

        $notionManager = new NotionManager();
        $notions = $notionManager->selectAllBySubject((int)$subjectId);

        return $this->twig->render(
            'Theme/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'subjectSelected' => $subject
            ]
        );
    }

    public function add(): string
    {
        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

                $name = trim($_POST['name']);
                $errors = [];

                if ($name == "") {
                    $errors['name'] = "Veuillez saisir le nom du sujet";
                }
                
                if (($this->subjectManager->isExist($name, (int)$_SESSION['theme_id']))) {
                    $errors['name'] = "Notion déjà existante";
                }

                if (!empty($errors)) {
                    return $this->twig->render(
                        'Subject/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter un nouveau sujet',
                            'errors' => $errors,
                            'themeId' => $_SESSION['theme_id']
                        ]
                    );
                }

                $newSubjectId = $this->subjectManager->insert((int)$_SESSION['theme_id'], $name);

                header("Location: /notion/add?subjectid=" . $newSubjectId);
                return "";
            }
        }

        return $this->twig->render(
            'Subject/add.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Ajouter un nouveau sujet',
                'themeId' => $_SESSION['theme_id']
            ]
        );
    }

    public function edit(int $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            header("Location: /");
        }

        $subject = $this->subjectManager->selectOneById($subjectId);

        if (isset($_POST['button']) && $_POST['button'] == "Valider") {

            $name = trim($_POST['name']);
            $errors = [];

            if (($this->subjectManager->isExist($name, (int)$_SESSION['theme_id']))) {
                $errors['name'] = "Notion déjà existante";
            }

            if ($name == "") {
                $errors['name'] = "Veuillez saisir le nom du sujet";
            }

            if (!empty($errors)) {
            
                return $this->twig->render(
                    'Subject/edit.html.twig',
                    [
                        'headerTitle' => $_SESSION['theme_name'],
                        'titleForm' => 'Modifier ce sujet',
                        'errors' => $errors,
                        'subject' => $subject
                    ]
                );
            }

            $this->subjectManager->update((int)$subjectId, $name);

            header("Location: /subject/show?id=" . $subjectId);
        }

        return $this->twig->render(
            'Subject/edit.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Modifier ce sujet',
                'subject' => $subject
            ]
        );
    }

    public function delete()
    {
        if (isset($_POST['response'])) {
            $this->subjectManager->delete((int)$_POST['response']);
            $route = '/theme/show?id=' . (int)$_SESSION['theme_id'];

            return ($route);
        }
    }
}
