<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Common;

/**
 * 定义返回码
 */
class RespCode
{
    public static $SUCCESS = 1000;

    public static $ERR_PARAM_VALIDATION = 2001;

    public static $ERR_LOGIN = 2002;

    public static $ERR_REGISTER = 2003;

    public static $ERR_UNAUTHENTICATED = 2004;

    public static $ERR_HTTP_REQUEST = 2005;

    public static $ERR_INTERNAL_SERVER = 3001;

    public static $ERR_NOT_FOUND = 30004;
}
