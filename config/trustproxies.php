<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Laravel mempercayai header X-Forwarded-* hanya dari daftar IP/CIDR ini.
    | Default: kosong (tidak ada proxy tepercaya) sehingga throttle & audit IP
    | memakai alamat asli klien. Jika aplikasi berada di belakang reverse proxy
    | (mis. Cloudflare), isi TRUST_PROXIES dengan daftar CIDR proxy yang dikenal.
    |
    | Contoh: TRUST_PROXIES=173.245.48.0/20,103.21.244.0/22,10.0.0.0/8
    |
    */

    'proxies' => array_values(array_filter(array_map('trim', explode(',', env('TRUST_PROXIES', ''))), fn ($ip) => $ip !== '')),
];
