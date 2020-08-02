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
