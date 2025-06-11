
<?php
// Contact form configuration
return [
    'email' => [
        'to' => 'mmpublicschool@gmail.com',
        'from_name' => 'Mary Matha Public School Website',
        'subject_prefix' => 'Contact Form: ',
    ],
    'validation' => [
        'required_fields' => ['name', 'email', 'subject', 'message'],
        'max_message_length' => 2000,
        'max_name_length' => 30,
    ],
    'logging' => [
        'enabled' => true,
        'file' => 'contact_submissions.log',
    ],
    'security' => [
        'rate_limit' => 10, // submissions per hour from an  IP
        'blocked_words' => ['spam', 'viagra', 'casino', 'rummy'], // Simple spam filter
    ]
];
?>
