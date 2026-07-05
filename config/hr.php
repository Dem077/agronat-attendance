<?php

return [
    'late_ot_request_hours'=> env('LATE_OT_REQUEST_HOURS',48),
    'late_grace_minutes' => (int) env('LATE_GRACE_MINUTES', 15),
    'late_sat_grace_minutes' => (int) env('LATE_SAT_GRACE_MINUTES', 15),
    'payroll_period_start_day' => (int) env('PAYROLL_PERIOD_START_DAY', 21),
    'payroll_period_end_day' => (int) env('PAYROLL_PERIOD_END_DAY', 20),
];