<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkGroupChat;
use App\Models\Wechat\WechatWorkGroupChatMember;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GroupChatService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkGroupChat::class;

    /**
     * 根据ID获取群聊信息
     * @param int $id
     * @return WechatWorkGroupChat
     */
    public function getById($id)
    {
        return with($this->dbQuery()->where('id', $id)->first());
    }

    /**
     * 根据chat_id获取群聊信息
     * @param string $chatId
     * @return WechatWorkGroupChat
     */
    public function getByChatId($chatId)
    {
        return with($this->dbQuery()->where('chat_id', $chatId)->first());
    }

    /**
     * 根据ChatID删除客户群（本地数据）
     * @param string $chatId
     * @return bool
     */
    public function deleteByChatIdOnlyLocal($chatId)
    {
        $this->dbQuery()->where('chat_id', $chatId)->delete();

        return true;
    }

    /**
     * 同步群成员数据到数据库中
     * @param array $data
     * @return WechatWorkGroupChatMember
     */
    protected function syncMemberData($data)
    {
        return WechatWorkGroupChatMember::unguarded(function () use ($data) {
            $groupChatMemberData = WechatWorkGroupChatMember::getAllowFields($data);
            $groupChatMemberData['corp_id'] = $this->corpId();
            $groupChatMemberData['invitor_userid'] = isset($data['invitor']) ? $data['invitor']['userid'] : '';

            return WechatWorkGroupChatMember::query()->updateOrCreate([
                'chat_id' => $groupChatMemberData['chat_id'],
                'userid' => $groupChatMemberData['userid'],
            ], $groupChatMemberData);
        });
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkGroupChat
     */
    protected function syncData($data)
    {
        return WechatWorkGroupChat::unguarded(function () use ($data) {
            $groupChatData = WechatWorkGroupChat::getAllowFields($data);
            $groupChatData['corp_id'] = $this->corpId();
            $groupChatData['created_at'] = Carbon::createFromTimestamp($data['create_time']);

            /** @var WechatWorkGroupChat $groupChat */
            $groupChat = $this->dbQuery()->updateOrCreate([
                'chat_id' => $data['chat_id'],
            ], $groupChatData);

            $groupChat->members = new Collection(array_map(function ($item) use ($data) {
                $item['chat_id'] = $data['chat_id'];

                return $this->syncMemberData($item);
            }, $data['member_list']));

            return $groupChat;
        });
    }

    /**
     * 同步群组数据
     * @param string $chatId
     * @return WechatWorkGroupChat|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncByChatId($chatId)
    {
        $result = $this->getOfQy($chatId);

        if (!$result) {
            return null;
        }

        return $this->syncData($result);
    }

    /**
     * 通过一组chatId数据同步群组数据
     * @param array $chatIdList
     * @return Collection|\Illuminate\Support\Collection
     */
    public function syncOfChatIdList($chatIdList)
    {
        return (new Collection($chatIdList))->map(function ($chatId) {
            return $this->syncByChatId($chatId);
        });
    }

    /**
     * 全量同步数据
     * @return Collection
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncAll($nextCursor = null)
    {
        $result = [];

        while (true) {
            $list = $this->getListOfQy($nextCursor);

            $result = array_merge($result, array_map(function ($item) {
                return $this->syncByChatId($item['chat_id']);
            }, $list['group_chat_list']));

            $nextCursor = $list['next_cursor'] ?? null;
            if (!$nextCursor) {
                break;
            }
        }

        return (new Collection($list))->filter();
    }

    /**
     * 获取群组信息（企微）
     * @param string $chatId
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOfQy($chatId)
    {
        $result = WechatResult::make($this->work()->external_contact->getGroupChat($chatId));

        return $result->isSucceeded() ? $result['group_chat'] : null;
    }

    /**
     * 获取群组列表信息（企微）
     * @param string $nextCursor
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getListOfQy($nextCursor = null)
    {
        $result = WechatResult::make(
            $this->work()->external_contact->getGroupChats([
                'cursor' => $nextCursor,
                'limit' => 1000,
            ])
        );

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 获取所有的ChatIds
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllChatIdOfQy()
    {
        $result = [];
        $nextCursor = null;

        while (true) {
            $list = $this->getListOfQy($nextCursor);

            $result = array_merge($result, $list['group_chat_list']);

            $nextCursor = $list['next_cursor'] ?? null;
            if (!$nextCursor) {
                break;
            }
        }

        return $result;
    }
}
