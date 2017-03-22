<?php

return [
    'image_system' => '/user/images/',
    'image_user_default' => 'user/images/default_user.png',
    'image_default' => 'questionDefault.png',
    'image_path' => 'image/',
    'image_question_path' => '/user/uploads/question/',
    'image_answer_path' => '/user/uploads/answer/',
    'image_path_system' => 'user/images/',
    'question' => [
        'not_required' => 0,
        'required' => 1,
    ],
    'paginate'=> 9,
    'type_answer' => [
        'radiobutton' => '1',
        'checkbox' => '2',
        'textfield' => '3',
        'selectbox' => '4',
    ],
    'result' => [
        'success' => 'true',
        'fail' => 'false',
    ],
    'feature' => 1,
    'not_feature' => 0,
    'mark' => 1,
    'unmark' => 0,
    'google' => 'google',
    'facebook' => 'facebook',
    'twitter' => 'twitter',
    'replace' => 'survey/result/',
    'required' => [
        'true' => 1,
        'false' => 0,
    ],
    'return' => [
        'bool' => 0,
        'view' => 1,
    ],
    'locale' => [
        'en',
        'vn',
        'jp',
    ],
    'language' => [
        'en' => 'English',
        'vn' => 'Việt Nam',
        'jp' => 'Japanese',
    ],
    'options' => [
        1,
        2,
        3,
    ],
    'key' => [
        'requireAnswer' => 1,
        'limitAnswer' => 2,
        'hideResult' => 3,
        'requireOnce' => 4,
        'tailMail' => 5,
    ],
    'require' => [
        'email' => 1,
        'name' => 2,
        'both' => 3,
    ],
    'listKey' => [
        1,
        2,
        3,
        4,
        5,
    ],
    'email_unidentified' => 0,
    'name_unidentified' => 1,
];
