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
// remove the following comment.

//require_once('../others/save_refresh_token.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    // load json from request body
    $request_params = json_decode(file_get_contents('php://input'), true);

    // validate state
    if ($request_params['state'] !== $_SESSION['state']) {
        render_error_json();
    }

    // token endpoint request
    $token_endpoint_request_params = [
        'client_id'    => CLIENT_ID,
        'redirect_uri' => REDIRECT_POST_ENDPOINT,
        'code'         => $request_params['code'],
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
            $jwt_claims = JWT::decode($json_body['id_token'], PUBLIC_KEY, array('RS256'));
        } catch (Exception $e) {
            error_log('jwt_decode failed because ' . $e . "\n", 3, ERROR_LOG_PATH);
            render_error_json();
        }

        
        //----------------------
        // Validate JSON Web Token

        $unixtime = time();
        // validate 'iss'(Issuer)
        if (!($jwt_claims->iss === MOBAGE_CONNECT_ISSUER)) {
            error_log('Issuer Claim is invalid.' . "\n", 3, ERROR_LOG_PATH);
            render_error_json();
        }
        // validate 'aud'(Audience)
        if (!($jwt_claims->aud === CLIENT_ID)) {
            error_log('Audience Claim is invalid.' . "\n", 3, ERROR_LOG_PATH);
            render_error_json();
        }
        // validate 'iat'(Issued At)
        if (!($jwt_claims->iat <= $unixtime)) {
            error_log('Issed At Claim is newer than now.' . "\n", 3, ERROR_LOG_PATH);
            render_error_json();
        }
        // validate 'exp'(Expiration Time)
        if (!($jwt_claims->exp >= $unixtime)) {
            error_log('Expiration Time Claim is older than now.' . "\n", 3, ERROR_LOG_PATH);
            render_error_json();
        }

        // setting session expire
        session_regenerate_id(true);
        session_set_cookie_params($jwt_claims->exp - $jwt_claims->iat);

        // store login session
        $_SESSION['user_id']       = $jwt_claims->sub;
        $_SESSION['access_token']  = $json_body['access_token'];
        $_SESSION['refresh_token'] = $json_body['refresh_token'];
        $_SESSION['session_state'] = $request_params['session_state'];

        // if you want to save the refresh_token on your GameServer,
        // remove the following comment. 

        // $refresh_token_expires_at = time() + (90 * 24 * 3600); # valid for 90 days
        //saveRefreshToken($_SESSION['user_id'], $_SESSION['refresh_token'], $refresh_token_expires_at);
    } else {
        render_error_json();
    }

    render_json([ 'success' => true, 'user_id' => $_SESSION['user_id'] ]);
}

function render_json(array $params) {
    echo json_encode($params);
    exit;
}

function render_error_json() {
    return render_json([ 'success' => false ]);
}
