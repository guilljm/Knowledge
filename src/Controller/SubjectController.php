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

            if (isset($_POST['button']) && $_POST['button'] == "Valider") {

                $name = trim($_POST['name']);

                if ($name == "") {
                    $nameErrors[] = "Veuillez saisir le champ";
                }

                $subjectManager = new SubjectManager();
                if (($subjectManager->getName($name, (int)$_SESSION['theme_id']))) {
                    $nameErrors[] = "Notion déjà existante";
                }

                if (!empty($nameErrors)) {

                    return $this->twig->render(
                        'Subject/add.html.twig',
                        [
                            'headerTitle' => $_SESSION['theme_name'],
                            'titleForm' => 'Ajouter un nouveau sujet',
                            'nameErrors' => $nameErrors,
                            'themeId' => $_SESSION['theme_id']
                        ]
                    );
                }

                $subjectManager->add((int)$_SESSION['theme_id'], $name);

                return $this->twig->render(
                    'Subject/add.html.twig',
                    [
                        'headerTitle' => $_SESSION['theme_name'],
                        'titleForm' => 'Ajouter un nouveau sujet',
                        'validationMessage' => 'Bravo ! le nouveau sujet ' . $name .  ' a bien été ajoutée.',
                        'themeId' => $_SESSION['theme_id']
                    ]
                );

                // header("Location: /theme/show?id=" . $_SESSION['theme_id']);
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
            return "Session variables undefined";
        }

        $subjectManager = new SubjectManager();
        $subject = $subjectManager->selectOneById($subjectId);

        if (isset($_POST['button']) && $_POST['button'] == "Valider") {

            // header("Location: /theme/show?id=" . $_SESSION['theme_id']);

            $name = trim($_POST['name']);

            if ($name == "") {
                $nameErrors[] = "Veuillez saisir le champ";

                return $this->twig->render(
                    'Subject/edit.html.twig',
                    [
                        'headerTitle' => $_SESSION['theme_name'],
                        'titleForm' => 'Modifier ce sujet',
                        'nameErrors' => $nameErrors,
                        'themeId' => $_SESSION['theme_id']
                    ]
                );
            }

            $subjectManager->update((int)$subjectId, $_POST['name']);

            return $this->twig->render(
                'Subject/add.html.twig',
                [
                    'headerTitle' => $_SESSION['theme_name'],
                    'titleForm' => 'Modifier ce sujet',
                    'name' => $subject['name'],
                    'validationMessage' => 'Bravo ! le nouveau sujet ' . $name .  ' a bien été modifié.',
                    'themeId' => $_SESSION['theme_id']
                ]
            );
        }

        return $this->twig->render(
            'Subject/edit.html.twig',
            [
                'headerTitle' => $_SESSION['theme_name'],
                'titleForm' => 'Modifier ce sujet',
                'name' => $subject['name'],
                'themeId' => $_SESSION['theme_id']
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
