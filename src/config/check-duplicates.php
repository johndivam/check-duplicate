<?php

return [
   'models' => [
        [
            'model' => \App\Models\Task::class,
            'columns' => ['project_id', 'name'],
            'with_deletes' => false  # Exclude soft-deleted records
        ],
        [
            'model' => \App\Models\TaskComment::class,
            'columns' => ['comment','user_id'],
            'with_deletes' => true # Include soft-deleted records
        ],
        # Add additional models here as needed
    ],
];