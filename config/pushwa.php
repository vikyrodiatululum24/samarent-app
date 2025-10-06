<?php

return [
  'base_url' => env('PUSHWA_BASE_URL', 'https://dash.pushwa.com/api'),
  'token' => env('PUSHWA_TOKEN'),
  // kamu bisa tambahkan pengaturan timeout, header, dsb
  'timeout' => 10, // detik
];
