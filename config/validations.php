<?php

// Contains validations used in multiple places.
// For example in store and update actions.
return [
    'book' => [
        'title' => 'required|max:255',
        'description' => 'required',
        'author_id' => 'exists:users,id',
    ]
];