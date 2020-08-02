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
use App\Service\DirServiceInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\PutMapping;
use Qbhy\HyperfAuth\Annotation\Auth;

/**
 * @Controller(prefix="api")
 * @Auth("jwt")
 */
class DirController extends AbstractController
{
    /**
     * @Inject
     * @var DirServiceInterface
     */
    protected $dirService;

    /**
     * @GetMapping(path="dirs")
     */
    public function list()
    {
        $query = [['status', '=', 1]];
        return $this->success($this->dirService->list($query));
    }

    /**
     * @PostMapping(path="dirs")
     */
    public function store()
    {
        if ($errMessage = $this->validate([
            'dirName' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }
        $dir = $this->removeNull($this->request->inputs(['dirName', 'icon', 'desc', 'pid']));
        $res = $this->dirService->add($dir);
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_INTERNAL_SERVER, $res['msg']);
        }

        return $this->success($res['data']);
    }

    /**
     * @PutMapping(path="dirs/{id}")
     */
    public function update()
    {
        if ($errMessage = $this->validate([
            'id' => 'required|integer|exists:dirs',
            'dirName' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }
        $dir = $this->removeNull($this->request->inputs(['id', 'dirName', 'icon', 'desc', 'pid']));

        $res = $this->dirService->edit($dir);
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_INTERNAL_SERVER, $res['msg']);
        }
        return $this->success($res['data']);
    }

    /**
     * @DeleteMapping(path="dirs/{id}")
     */
    public function destroy(int $id)
    {
        if ($errMessage = $this->dirService->remove($id)) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }

        return $this->success(null);
    }

    /**
     * @GetMapping(path="dirs/{id}")
     */
    public function show(int $id)
    {
        return $this->success($this->dirService->get($id));
    }
}
