<?php

// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [
    '' => ['HomeController', 'index'],
    'theme/show' => ['ThemeController', 'show', ['id']],
    'subject/show' => ['SubjectController', 'show', ['id']],
    'subject/add' => ['SubjectController', 'add'],
    'subject/edit' => ['SubjectController', 'edit', ['id']],
    'subject/delete' => ['SubjectController', 'delete'],
    'notion/show' => ['NotionController', 'show', ['id']],
    'notion/add' => ['NotionController', 'add', ['subjectid']],
    'notion/edit' => ['NotionController', 'edit', ['id']],
    'notion/delete' => ['NotionController', 'delete'],
    'exercise/add' => ['ExerciseController', 'add', ['notionid']],
    'exercise/edit' => ['ExerciseController', 'edit', ['id']],
    'exercise/delete' => ['ExerciseController', 'delete']
];
