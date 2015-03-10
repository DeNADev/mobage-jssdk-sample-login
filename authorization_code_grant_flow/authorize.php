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

if (!isset($_SESSION['user_id']) || !isset($_SESSION['access_token'])) {
    session_set_cookie_params(0);

    //create state
    $state = uniqid("mobage-connect_", true);
    $_SESSION['state'] = $state;

    $authorize_endpoint_request_params = [
        'client_id'     => CLIENT_ID,
        'redirect_uri'  => REDIRECT_GET_ENDPOINT,
        'response_type' => 'code',
        'scope'         => 'openid common_api',
        'state'         => $state
    ];

    $query = http_build_query($authorize_endpoint_request_params);
    header('Location: ' . AUTHORIZE_ENDPOINT_URL . '?' . $query);
    exit;
}
?>
<!DOCTYPE html>
<html lang='ja'>
<head>
	<meta charset='UTF-8'>
	<title>Login (Authorization Code Grant Flow)</title>
</head>
<body>
	<h1>Login (Authorization Code Grant Flow)</h1>
</body>
</html>
