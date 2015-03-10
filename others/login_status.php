<!--
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
-->

<?php
require_once('../config.php');
?>
<!DOCTYPE html>
<html lang='ja'
    data-client-id="<?php echo CLIENT_ID ?>"
    data-redirect-uri="<?php echo REDIRECT_POST_ENDPOINT ?>"
    data-logout-uri="<?php echo REDIRECT_LOGOUT_ENDPOINT ?>"
<?php if(isset($_SESSION['state'])): ?>
    data-state="<?php echo $_SESSION['state'] ?>"
<?php endif; ?>
<?php if(isset($_SESSION['session_state'])): ?>
    data-session-state="<?php echo $_SESSION['session_state'] ?>"
<?php endif; ?>
>
<head>
	<meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width'>
	<title>Check Login Status</title>
</head>
<body>
	<h1>Check Login Status</h1>
	<input id='mobage-login'  type='hidden'>
    <input id='mobage-logout' type='hidden'>
    <p><a href='../index.php'>Go to Top</a></p>

    <br>
    <p id='my-mobage-info'></p>

	<script type='text/javascript'>
		(function() {
			var script   = document.createElement('script');
			script.type  = 'text/javascript';
			script.async = true;
			script.src   = "<?php echo JSSDK_PATH ?>";
			document.getElementsByTagName('script')[0].parentNode.appendChild(script);
		})();
        console.log('jssdk is loaded');
    </script>
	<script type='text/javascript' src="../main.js?time=<? echo time() ?>"></script>
</body>
</html>
