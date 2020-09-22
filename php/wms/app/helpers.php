<?php

/**
 * 辅助函数
 * User: DELL
 * Date: 2018/6/27
 * Time: 11:33
 */

/**
 * API返回格式定义
 *
 * @param integer $code
 * @param string $message
 * @param array $data
 * @return array
 */
function sendData($code = 200, $message = '', $data = [])
{
    $result = [
        'code' => $code,
        'message' => $message,
        'data' => $data
    ];

    return response()->json($result);
}

/**
 * API返回格式定义
 *
 * @param string $string
 * @return string|null
 */
function parseRsa($password)
{
    $private_key = str_replace("\r", '', file_get_contents(public_path('id_rsa')));
    $encrypt_data = base64_decode($password);
    openssl_private_decrypt($encrypt_data, $decrypt_data, $private_key);
    return $decrypt_data;
}
