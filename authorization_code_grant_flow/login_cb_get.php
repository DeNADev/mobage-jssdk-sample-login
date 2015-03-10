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
 
require_once('../config.php');
require_once('../JWT/JWT.php');

// if you want to save the refresh_token on your GameServer,
// remove the following commentout.

//require_once('../others/save_refresh_token.php');


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8');


    // validate state
    if (isset($_SESSION['state']) && 
        $_GET['state'] !== $_SESSION['state']) {
        header('Location: ' . CLIENT_URI);
        exit;
    }

    // check error in authorization error response
    if (isset($_GET['error'])) {
        header('Location: ' . CLIENT_URI);
        exit;
    }


    // token endpoint request
    $token_endpoint_request_params = [
        'client_id'    => CLIENT_ID,
        'redirect_uri' => REDIRECT_GET_ENDPOINT,
        'code'         => $_GET['code'],
        'grant_type'   => 'authorization_code'
    ];

    $headers = [
        'Content-Type: application/x-www-form-urlencoded',
        'Connection: close',
        'Authorization: ' . sprintf("Basic %s", base64_encode(CLIENT_ID . ":" . CLIENT_SECRET)),
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'content' => http_build_query($token_endpoint_request_params),
            'header'  => implode("\r\n", $headers),
        ]
    ];

    $contents = file_get_contents(TOKEN_ENDPOINT_URL, false, stream_context_create($options));

    if ($contents) {
        $json_body = json_decode($contents, true);

        try {
            $jwt_claims = JWT::decode($json_body['id_token'], PUBLIC_KEY);
        } catch (Exception $e) {
            error_log('jwt_decode failed because ' . $e . "\n", 3, ERROR_LOG_PATH);
            header('Location: ' . CLIENT_URI);
            exit;
        }


        //----------------------
        // Validate JSON Web Token

        $unixtime = time();
        // validate 'iss'(Issuer)
        if (!($jwt_claims->iss === MOBAGE_CONNECT_ISSUER)) {
            error_log('Issuer Claim is invalid.' . "\n", 3, ERROR_LOG_PATH);
            header('Location: ' . CLIENT_URI);
            exit;
        }
        // validate 'aud'(Audience)
        if (!($jwt_claims->aud === CLIENT_ID)) {
            error_log('Audience Claim is invalid.' . "\n", 3, ERROR_LOG_PATH);
            header('Location: ' . CLIENT_URI);
            exit;
        }
        // validate 'iat'(Issued At)
        if (!($jwt_claims->iat <= $unixtime)) {
            error_log('Issed At Claim is newer than now.' . "\n", 3, ERROR_LOG_PATH);
            header('Location: ' . CLIENT_URI);
            exit;
        }
        // validate 'exp'(Expiration Time)
        if (!($jwt_claims->exp >= $unixtime)) {
            error_log('Expiration Time Claim is older than now.' . "\n", 3, ERROR_LOG_PATH);
            header('Location: ' . CLIENT_URI);
            exit;
        }


        // setting session expire
        session_regenerate_id(true);
        session_set_cookie_params($jwt_claims->exp - $jwt_claims->iat);

        // store login session
        $_SESSION['user_id']       = $jwt_claims->sub;
        $_SESSION['access_token']  = $json_body['access_token'];
        $_SESSION['refresh_token'] = $json_body['refresh_token'];
        $_SESSION['session_state'] = $_GET['session_state'];
        

        // if you want to save the refresh_token on your GameServer,
        // remove the following commentout
        
        // $refresh_toke_expires_at = time() + (90 * 24 * 3600); # valid for 90 days
        //saveRefreshToken($_SESSION['user_id'], $_SESSION['refresh_token'], $refresh_toke_expires_at);
    } else {
        header('Location: ' . CLIENT_URI);
        exit;
    }

    // redirect to required login endpoint
    $post_login_redirect_uri = $_SESSION['post_login_redirect_uri'];

    unset($_SESSION['post_login_redirect_uri']);
    header('Location: ' . $post_login_redirect_uri);
    exit;
}
