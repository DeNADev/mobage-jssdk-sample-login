<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2014-2015 DeNA Co., Ltd.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
 
session_start();

define('BASE_URI',  'http://localhost');
define('ERROR_LOG_PATH', '/var/log/php/error.log');
define('JSSDK_PATH' , 'https://cdn-sb-connect.mobage.jp/jssdk/mobage-jssdk-client.2.5.0.min.js');

// Select 'Dashboard > Applications > [YourAppName] > Mobage' on Mobage Developers Site.
// Select 'SPWeb > Mobage Connect Information > for sandbox environment' from upper tabs.
// You can find following information on the page. Copy the values, and Replace them.
define('SPWEB_CLIENT_ID',       'YOUR_SPWEB_CLIENT_ID');
define('SPWEB_CLIENT_SECRET',   'YOUR_SPWEB_CLIENT_SECRET');

define('ANDROID_CLIENT_ID',     'YOUR_ANDROID_CLIENT_ID');
define('ANDROID_CLIENT_SECRET', 'YOUR_ANDROID_CLIENT_SECRET');

define('IOS_CLIENT_ID',         'YOUR_IOS_CLIENT_ID');
define('IOS_CLIENT_SECRET',     'YOUR_IOS_CLIENT_SECRET');

$CLIENT_SECRETS = [ 
    SPWEB_CLIENT_ID   => SPWEB_CLIENT_SECRET,
    ANDROID_CLIENT_ID => ANDROID_CLIENT_SECRET,
    IOS_CLIENT_ID     => IOS_CLIENT_SECRET
];

$client_id     = SPWEB_CLIENT_ID;
if (isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT'])) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_agent, 'Mobage-ShellApp-SDK-Android') !== false) {
        $client_id     = ANDROID_CLIENT_ID;
    } elseif (strpos($user_agent, 'Mobage-ShellApp-SDK-iOS') !== false) {
        $client_id     = IOS_CLIENT_ID;
    }   
}
define('CLIENT_ID',     $client_id);
define('CLIENT_SECRET', $CLIENT_SECRETS[$client_id]);


// Write down the following endpoint using Programming Guide as a reference.
// (The following values are for sandbox environment)
define('AUTHORIZE_ENDPOINT_URL', 'https://sb-connect.mobage.jp/connect/1.0/services/authorize');
define('TOKEN_ENDPOINT_URL',     'https://sb-connect.mobage.jp/connect/1.0/api/token');
define('LOGOUT_CONFIRM_URL',     'https://sb-connect.mobage.jp/logout');
define('MOBAGE_CONNECT_ISSUER',  'https://sb-connect.mobage.jp');

// Select 'Dashboard > Applications > [YourAppName] > Mobage' on Mobage Developers Site.
// Select 'SPWeb > Mobage Connect Information > for sandbox environment' from upper tabs.
// You should register the following information on the Mobage Developers Site.
define('CLIENT_URI', BASE_URI . '/mobage-jssdk-sample-login');
define('REDIRECT_POST_ENDPOINT', CLIENT_URI . '/hybrid_flow/login_cb_post.php');
define('REDIRECT_GET_ENDPOINT',  CLIENT_URI . '/authorization_code_grant_flow/login_cb_get.php');
define('REDIRECT_LOGOUT_ENDPOINT', CLIENT_URI . '/others/logout.php');

// The following is a Public Key of JSON Web Token.
// (You can find it on the Programming Guide)
// for sandbox environment : X509 format
$public_key = <<<EOT
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuDopA35ZLa1sgi2QTFbZ
jH63BrhXw4evehjDiLHrSc5s+jKMSqfd6BLoQhN7jcOBnofQB/3rEoy6YXkW58lE
tVmekQtYAHh11oB8TBBqzNZP1QxKXWjz8Jely5bJZHztZEzfddDR7yVZF0VSEa0K
jiHbqCdAqXKYuAzUMN4dFyS/q0JXvGArJq/LXyVC3EptcZki02p3Nd6KDZHW7hcj
+p0xgYNiGCHO7yLf3uHP+7pak5TW0dWfMC9fl1/oYFILuasW7OV75+vJGs8d92jo
EC/Lx3S8+gi3z0CWMnCKrWiTtaAKAq8Wyp4Go8WczOQ1rP+bzDj7b/9M3xrjqY3F
0wIDAQAB
-----END PUBLIC KEY-----
EOT;

define('PUBLIC_KEY', $public_key);

// The following parameters are the setting on database.
// If you would like to use batch program on GameServer,
// you should save refresh_token on database.
define('PDO_DSN',      'mysql:dbname=mobage_jssdk_sample;host=localhost');
define('PDO_USERNAME', 'YOUR_USERNAME');
define('PDO_PASSWORD', 'YOUR_PASSWORD');
