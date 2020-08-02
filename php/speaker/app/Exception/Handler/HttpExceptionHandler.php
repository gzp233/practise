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
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler as HyperfHttpExceptionHandler;
use Hyperf\Utils\Codec\Json;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends HyperfHttpExceptionHandler
{
    /**
     * Handle the exception, and return the specified result.
     * @return ResponseInterface
     */
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $status = $throwable->getStatusCode();
        $body = [
            'code' => RespCode::$ERR_HTTP_REQUEST,
            'msg' => $throwable->getMessage(),
        ];
        $this->logger->debug($this->formatter->format($throwable));

        $this->stopPropagation();

        return $response->withStatus($status)->withBody(new SwooleStream(Json::encode($body)));
    }
}
