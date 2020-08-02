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
namespace App\Exception\Handler;

use App\Common\RespCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Codec\Json;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Qbhy\HyperfAuth\Exception\UnauthorizedException;
use Throwable;

class JwtExceptionHandler extends ExceptionHandler
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('log', 'default');
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // TODO 暂时无法刷新token，插件未抛出TokenExpiredException异常
        $this->stopPropagation();
        if ($throwable instanceof UnauthorizedException) {
            $status = 401;
            $body = [
                'code' => RespCode::$ERR_UNAUTHENTICATED,
                'msg' => 'Unauthenticated.',
            ];
        } else {
            $status = $throwable->status;
            $body = [
                'code' => RespCode::$ERR_PARAM_VALIDATION,
                'msg' => $throwable->validator->errors()->first(),
            ];
        }
        $this->logger->debug('JWT验证异常:' . $throwable->getMessage());

        return $response->withStatus($status)->withBody(new SwooleStream(Json::encode($body)));
    }

    public function isValid(Throwable $throwable): bool
    {
        if ($throwable instanceof UnauthorizedException) {
            return true;
        }
        if ($throwable instanceof ValidationException) {
            return true;
        }
        return false;
    }
}
