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
namespace App\Service;

use App\Model\Dir;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class DirService implements DirServiceInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('log', 'default');
    }

    public function list(array $query)
    {
        return Dir::query()->where($query)->get();
    }

    public function add(array $params)
    {
        try {
            if ($dir = Dir::query()->create($params)) {
                return ['msg' => '', 'data' => $dir];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return ['msg' => '新增失败'];
    }

    public function edit(array $params)
    {
        try {
            if ($dir = Dir::query()->find($params['id'])->update($params)) {
                return ['msg' => '', 'data' => $dir];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return ['msg' => '更新失败'];
    }

    public function remove(int $id)
    {
        $dir = Dir::query()->find($id);
        if (! $dir) {
            return '该目录不存在';
        }
        try {
            $dir->status = 0;
            $dir->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return '删除失败';
        }

        return '';
    }

    public function get(int $id)
    {
        return Dir::query()->with('tips')->find($id);
    }
}
