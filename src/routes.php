<?php

// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [
    '' => ['ThemeController', 'index'],
    'theme/show' => ['ThemeController', 'show', ['id']],
    'subject/show' => ['SubjectController', 'show', ['id']],
    'notion/show' => ['NotionController', 'show', ['id']],
    'notion/add' => ['NotionController', 'add', ['idsubject']],
    'notion/update' => ['NotionController', 'update', ['id']],
    'notion/delete' => ['NotionController', 'delete', ['id']],
    'exercise/add' => ['ExerciseController', 'add', ['idnotion']],
    'exercise/update' => ['ExerciseController', 'update', ['id']],
    'exercise/delete' => ['ExerciseController', 'delete', ['id']]

];
