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

use App\Model\User;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Utils\ApplicationContext;
use Psr\Log\LoggerInterface;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthManager;

class AuthService implements AuthServiceInterface
{
    /**
     * @Inject
     * @var AuthManager
     */
    protected $auth;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('log', 'default');
    }

    // 用户登录
    public function login(array $credential): array
    {
        $user = User::query()->where('username', $credential['username'])->first();
        if (! $user->status) {
            return ['msg' => '用户未启用'];
        }
        if (! password_verify($credential['password'], $user->password)) {
            return ['msg' => '用户或密码错误'];
        }

        return ['msg' => '', 'data' => $user];
    }

    // 用户注册
    public function register(array $params)
    {
        try {
            $generator = ApplicationContext::getContainer()->get(IdGeneratorInterface::class);
            $params['id'] = $generator->generate();
            $params['password'] = password_hash($params['password'], PASSWORD_DEFAULT);
            $user = User::query()->create($params);
            return ['msg' => '', 'data' => $user];
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return ['msg' => '服务器内部错误'];
        }
    }

    // 获取token
    public function getToken(Authenticatable $user)
    {
        return [
            'ttl' => config('auth.guards.jwt.ttl'),
            'token' => 'Bearer ' . $this->auth->guard('jwt')->login($user),
        ];
    }

    public function logout()
    {
        $this->auth->guard('jwt')->logout();
    }
}
