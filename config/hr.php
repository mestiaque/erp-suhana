<?php

return [
    // ...other HR config...

    // Helper to get all HR options (for controllers/views)
    'options' => function() {
        return \App\Services\HrOptionsService::getOptions();
    },
];
