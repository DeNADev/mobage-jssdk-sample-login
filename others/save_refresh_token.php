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


function saveRefreshToken($user_id, $refresh_token, $expires_at) {
    try {
        
        $dbh = new PDO(
                PDO_DSN, 
                PDO_USERNAME, 
                PDO_PASSWORD);
        $sql = 'insert into user_tokens
            (user_id, client_id, refresh_token, expires_at) values
            (?,?,?,?)
            on duplicate key update refresh_token=?, expires_at=?;';
        $sth = $dbh->prepare($sql);
        $dbh->beginTransaction();
        $sth->execute(array(
                    $user_id, CLIENT_ID, $refresh_token, $expires_at,
                    $refresh_token, $expires_at
                    ));
        $dbh->commit();

    } catch (PDOException $e) {
        error_log('PDO error ' . $e . "\n", 3, ERROR_LOG_PATH);
        die();
    }   
}
?>
