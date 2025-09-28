<?php

return [
    'ttl'           => env('MFA_CODE_TTL', 300),  // segundos
    'window'        => 1,                         // TOTP: aceitar ±1 período
    'recovery_count'=> 10,
    'recovery_len'  => 5,                         // 5-5 => XXXXX-YYYYY
];
