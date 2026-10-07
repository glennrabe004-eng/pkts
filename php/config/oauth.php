<?php
/*
 * Social login provider credentials.
 * Fill these in with real values from each provider's developer console.
 * Until then, the login buttons show a clear "not configured" message
 * instead of failing silently.
 *
 * Redirect URI to register with EVERY provider (must match exactly):
 *   <your-site>/pkts-main/php/oauth_callback.php
 */
return [
    'google' => [
        'client_id'     => 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri'  => ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/pkts-main/php/oauth_callback.php'),
        'auth_url'      => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url'     => 'https://oauth2.googleapis.com/token',
        'userinfo_url'  => 'https://openidconnect.googleapis.com/v1/userinfo',
        'scope'         => 'openid email profile',
    ],
    'facebook' => [
        'client_id'     => 'YOUR_FACEBOOK_APP_ID',
        'client_secret' => 'YOUR_FACEBOOK_APP_SECRET',
        'redirect_uri'  => ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/pkts-main/php/oauth_callback.php'),
        'auth_url'      => 'https://www.facebook.com/v18.0/dialog/oauth',
        'token_url'     => 'https://graph.facebook.com/v18.0/oauth/access_token',
        'userinfo_url'  => 'https://graph.facebook.com/me?fields=first_name,last_name,email',
        'scope'         => 'email public_profile',
    ],
    'apple' => [
        'client_id'        => 'YOUR_APPLE_SERVICE_ID', // Apple Services ID (e.g. com.example.appleid)
        'team_id'          => 'YOUR_APPLE_TEAM_ID',    // 10-character Team ID
        'key_id'           => 'YOUR_APPLE_KEY_ID',     // Auth Key ID (from the .p8 download)
        'private_key'      => '',                       // Paste the FULL contents of the .p8 key file here
        'private_key_file' => __DIR__ . '/apple_key.p8', // OR put the .p8 file here (used if private_key is empty)
        'redirect_uri'     => ((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . '/pkts-main/php/oauth_callback.php'),
        'auth_url'         => 'https://appleid.apple.com/auth/authorize',
        'token_url'        => 'https://appleid.apple.com/auth/token',
        'userinfo_url'     => '',
        'scope'            => 'name email',
    ],
];
