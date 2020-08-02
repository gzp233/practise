<?php

namespace App\Handlers;

use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Illuminate\Support\Facades\Log;

/**
 * @see https://wiki.swoole.com/wiki/page/400.html
 */
class WebSocketHandler implements WebSocketHandlerInterface
{
    // 聊天记录表
    private $chatroomTable;
    private $userTable;

    // 声明没有参数的构造函数
    public function __construct()
    {
        $this->chatroomTable = app('swoole')->chatroomTable;
        $this->userTable = app('swoole')->userTable;
    }

    public function onOpen(Server $server, Request $request)
    {
        // 保存用户和fd的映射
        $user = auth('api')->setToken($request->get['token'])->user();
        $username = $user->username;
        // 先检查，如果该用户已登录，就把原来的登录的踢下线
        $fdInfo = $this->userTable->get('username:' . $username);
        if ($fdInfo !== false) {
            // $this->unbindUser($username, $fdInfo['value']);
            $server->close($fdInfo['value']);
        }
        $this->userTable->set('username:' . $username, ['value' => $request->fd]);
        $this->userTable->set('fd:' . $request->fd, ['value' => $username]);
        $msg = $this->getMesage(0, 'system', "欢迎".$username."进入聊天室。");
        $this->notifyAll($server, $msg);
        // 刷新用户列表
        $this->notifyUserChange($server);
    }

    public function onMessage(Server $server, Frame $frame)
    {
        $username = $this->userTable->get('fd:' . $frame->fd);
        $msg = $this->getMesage(1, $username['value'],  $frame->data);
        $this->notifyAll($server, $msg);
    }

    public function onClose(Server $server, $fd, $reactorId)
    {
        // 解除用户和fd的映射
        $username = $this->userTable->get('fd:' . $fd);
        if ($username !== false) {
            $this->unbindUser($username['value'], $fd);
        }
        $msg = $this->getMesage(0, 'system',  "用户".$username['value']."离开了聊天室。");
        $this->notifyAll($server, $msg);
        // 刷新用户列表
        $this->notifyUserChange($server);
    }

    // 解除用户和fd的映射
    private function unbindUser($username, $fd)
    {
        $this->userTable->del('username:' . $username);
        $this->userTable->del('fd:' . $fd);
    }

    // 通知所有人
    private function notifyAll($server, $data)
    {
        foreach ($server->connections as $fd) {
            if ($server->isEstablished($fd)) {
                $server->push($fd, $data);
            }
        }
    }

    // $type 0=system 1=user 2=userList
    private function getMesage($type = 0, $from = 'system', $message)
    {
        $data = [
            'type' => $type,
            'from' => $from,
            'message' => $message
        ];
        return json_encode($data);
    }

    private function notifyUserChange($server) {
        $userList = $this->getUserList($server);
        $msg = $this->getMesage(2, '', $userList);
        $this->notifyAll($server, $msg);
    }

    // 获取在线用户列表
    private function getUserList($server)
    {
        $userList = [];
        foreach ($server->connections as $fd) {
            if ($server->isEstablished($fd)) {
                $username = $this->userTable->get('fd:' . $fd);
                $userList[] = $username['value'];
            }
        }

        return $userList;
    }
}
