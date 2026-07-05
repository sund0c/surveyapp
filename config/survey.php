<?php

return [
    // Max allowed difference (seconds) between client X-Timestamp and server time.
    // Combined with the nonce cache, this bounds the replay window.
    'hmac_skew_seconds' => env('SURVEY_HMAC_SKEW_SECONDS', 300),
];
