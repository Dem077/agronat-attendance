<?php

return [
    'late_ot_request_hours'=> env('LATE_OT_REQUEST_HOURS',48),
    'late_grace_minutes' => (int) env('LATE_GRACE_MINUTES', 15),
    'late_sat_grace_minutes' => (int) env('LATE_SAT_GRACE_MINUTES', 15),
];