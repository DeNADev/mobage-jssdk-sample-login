<?php

/*
 * This function was taken from JWT/JWT.php
 */
function urlSafeB64Decode($b64)
{
    $b64 = str_replace(array('-', '_'),
        array('+', '/'),
        $b64);

    return base64_decode($b64);
}

/*
 * Only RS256 is allowed
 */
function checkSignatureAlgorithm($jwt) {
    $tks = explode('.', $jwt);

    if (count($tks) != 3) {
        throw new Exception('Wrong number of segments');
    }

    list($headb64, $payloadb64, $cryptob64) = $tks;

    if (null === ($header = json_decode(urlsafeB64Decode($headb64)))) {
        throw new Exception('Invalid segment encoding');
    }

    if (! isset($header -> alg) || $header -> alg !== 'RS256') {
        throw new Exception('Invalid signature algorithm');
    }
}
