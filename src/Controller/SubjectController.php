<?php

namespace App\Controller;

use App\Model\NotionManager;
use App\Model\SubjectManager;


class SubjectController extends AbstractController
{
    public function show(string $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {

            return "Session variables undefined";
        }

        //Récuperer tous les sujets du thème
        $subjectManager = new SubjectManager();
        $subjects = $subjectManager->selectAllByTheme((int)$_SESSION['theme_id']);
        $subject = $subjectManager->selectOneById($subjectId);

        //Récuperer toutes les notions du sujet
        $notionManager = new NotionManager();
        $notions = $notionManager->selectAllBySubject((int)$subjectId);

        return $this->twig->render(
            'Notion/index.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'subjects' => $subjects,
                'notions' => $notions,
                'subjectname' => $subject['name'],
                'subjectId' => $subjectId
            ]
        );
    }

    public function add(): string
    {

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button'])) {
                if ($_POST['button'] == "Annuler") {

                    header("Location: /theme/show?id=" . $_SESSION['theme_id']);
                    return "";
                }
            }

            $name = $_POST['name'];

            $subjectManager = new SubjectManager();
            $subjectManager->add((int)$_SESSION['theme_id'], $name);

            header("Location: /theme/show?id=" . $_SESSION['theme_id']);

            return "";
        }

        return $this->twig->render(
            'Subject/add.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Ajouter un nouveau sujet'
            ]
        );
    }

    public function edit(int $subjectId): string
    {
        if (!is_numeric($subjectId)) {
            header("Location: /");
        }

        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $subjectManager = new SubjectManager();
        $subject = $subjectManager->selectOneById($subjectId);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            if (isset($_POST['button'])) {
                if ($_POST['button'] == "Annuler") {

                    header("Location: /theme/show?id=" . $_SESSION['theme_id']);
                    return "";
                }

                if ($_POST['button'] == "Valider") {
                    $subjectManager->update((int)$subjectId, $_POST['name']);

                    header("Location: /theme/show?id=" . $_SESSION['theme_id']);
                }
            }
        }


        return $this->twig->render(
            'Subject/edit.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Modifier ce sujet',
                'name' => $subject['name']
            ]
        );
    }

    public function delete(string $subjectId): string
    {

        if (!is_numeric($subjectId)) {
            header("Location: /");
        }


        if (!isset($_SESSION['theme_id']) || !isset($_SESSION['theme_name'])) {
            return "Session variables undefined";
        }

        $subjectManager = new SubjectManager();

        $subjectManager->delete((int)$subjectId);

        header("Location: /theme/show?id=" . $_SESSION['theme_id']);
        return "";
    }
}
