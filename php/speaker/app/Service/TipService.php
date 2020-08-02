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

use App\Model\Tip;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;

class TipService implements TipServiceInterface
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
        return Tip::query()->where($query)->paginate();
    }

    public function add(array $params)
    {
        try {
            $generator = ApplicationContext::getContainer()->get(IdGeneratorInterface::class);
            $params['id'] = $generator->generate();
            if ($tip = Tip::query()->create($params)) {
                return ['msg' => '', 'data' => $tip];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return ['msg' => '新增失败'];
    }

    public function edit(array $params)
    {
        try {
            if ($tip = Tip::query()->find($params['id'])->update($params)) {
                return ['msg' => '', 'data' => $tip];
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return ['msg' => '更新失败'];
    }

    public function remove(int $id)
    {
        $tip = Tip::query()->find($id);
        if (! $tip) {
            return '该条目不存在';
        }
        try {
            $tip->status = 0;
            $tip->save();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return '删除失败';
        }

        return '';
    }

    public function get(int $id)
    {
        return Tip::query()->with('dir')->find($id);
    }
}
