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
namespace App\Controller;

use App\Common\RespCode;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @Inject
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    // 成功返回
    protected function success($data)
    {
        $result = [
            'code' => RespCode::$SUCCESS,
            'msg' => '',
            'data' => $data,
        ];

        return $this->response->json($result);
    }

    // 失败返回
    protected function error(int $code, string $msg)
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => null,
        ];

        return $this->response->json($result);
    }

    // 参数验证
    protected function validate(array $rules): string
    {
        $validator = $this->validationFactory->make(
            $this->request->all(),
            $rules
        );
        if ($validator->fails()) {
            return $validator->errors()->first();
        }

        return '';
    }

    // 剔除为null的数据
    protected function removeNull(array $array)
    {
        foreach ($array as $key => $value) {
            if ($value == null) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
