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
use App\Service\TipServiceInterface;
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
class TipController extends AbstractController
{
    /**
     * @Inject
     * @var TipServiceInterface
     */
    protected $tipService;

    /**
     * @GetMapping(path="tips")
     */
    public function tips()
    {
        $query = [['status', '=', 1]];
        $title = $this->request->input('title', '');
        if ($title) {
            $query[] = ['title', 'like',  '%' . $title . '%'];
        }
        $tips = $this->tipService->list($query);

        return $this->success($tips);
    }

    /**
     * @PostMapping(path="tips")
     */
    public function store()
    {
        if ($errMessage = $this->validate([
            'title' => 'required',
            'dirId' => 'required|numeric',
            'content' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }
        $tip = $this->request->inputs(['dirId', 'title', 'content']);
        $res = $this->tipService->add($tip);
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_INTERNAL_SERVER, $res['msg']);
        }

        return $this->success($res['data']);
    }

    /**
     * @PutMapping(path="tips/{id}")
     */
    public function update()
    {
        if ($errMessage = $this->validate([
            'id' => 'required|integer|exists:tips',
            'title' => 'required',
            'dirId' => 'required|numeric',
            'content' => 'required',
        ])) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }
        $tip = $this->request->inputs(['id', 'dirId', 'title', 'content']);

        $res = $this->tipService->edit($tip);
        if ($res['msg']) {
            return $this->error(RespCode::$ERR_INTERNAL_SERVER, $res['msg']);
        }
        return $this->success($res['data']);
    }

    /**
     * @DeleteMapping(path="tips/{id}")
     */
    public function destroy(int $id)
    {
        if ($errMessage = $this->tipService->remove($id)) {
            return $this->error(RespCode::$ERR_PARAM_VALIDATION, $errMessage);
        }

        return $this->success(null);
    }

    /**
     * @GetMapping(path="tips/{id}")
     */
    public function show(int $id)
    {
        return $this->success($this->tipService->get($id));
    }
}
