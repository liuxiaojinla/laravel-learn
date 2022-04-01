<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkContact;
use App\Models\Wechat\WechatWorkContactFollowUser;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ContactService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkContact::class;

    /**
     * @var UserService
     */
    protected $user;

    /**
     * 根据ID获取客户信息
     * @param int $id
     * @return WechatWorkContact
     */
    public function getById($id)
    {
        return with($this->dbQuery()->where('id', $id)->first());
    }

    /**
     * 根据external_user_id获取客户信息
     * @param string $externalUserid
     * @return WechatWorkContact
     */
    public function getByExternalUserId($externalUserid)
    {
        return with($this->dbQuery()->where('external_user_id', $externalUserid)->first());
    }

    /**
     * 根据unionid获取客户信息
     * @param string $unionid
     * @return WechatWorkContact
     */
    public function getByUnionid($unionid)
    {
        return with($this->dbQuery()->where('unionid', $unionid)->first());
    }

    /**
     * 根据外部联系人ID删除客户（本地数据）
     * @param string $externalUserid
     * @return bool
     */
    public function deleteByExternalUserIdOnlyLocal($externalUserid)
    {
        $this->dbQuery()->where('external_user_id', $externalUserid)->delete();

        return true;
    }

    /**
     * 删除客户好友关系（本地数据）
     * @param string $externalUserid
     * @param string $userid
     */
    public function deleteFollowUserOnlyLocal($externalUserid, $userid)
    {
        WechatWorkContactFollowUser::query()->where([
            'corp_id' => $this->corpId(),
            'external_user_id' => $externalUserid,
        ])->where('userid', $userid)->delete();
    }

    /**
     * 同步好友关系到数据库中
     * @param array $data
     * @return WechatWorkContactFollowUser
     */
    protected function syncFollowUserData($data)
    {
        return WechatWorkContactFollowUser::unguarded(function () use ($data) {
            $followUserData = WechatWorkContactFollowUser::getAllowFields($data);
            $followUserData['corp_id'] = $this->corpId();
            $followUserData['created_at'] = Carbon::createFromTimestamp($data['createtime']);

            return WechatWorkContactFollowUser::withTrashed()->updateOrCreate([
                'corp_id' => $followUserData['corp_id'],
                'external_userid' => $followUserData['external_userid'],
                'userid' => $followUserData['userid'],
            ], $followUserData);
        });
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkContact
     */
    public function syncOfRawData($data)
    {
        return WechatWorkContact::unguarded(function () use ($data) {
            $contactData = WechatWorkContact::getAllowFields($data['external_contact']);
            $contactData['corp_id'] = $this->corpId();

            /** @var WechatWorkContact $contact */
            $contact = $this->dbWithTrashedQuery()->updateOrCreate([
                'corp_id' => $contactData['corp_id'],
                'external_userid' => $contactData['external_userid'],
            ], $contactData);

            $contact->follow_users = new Collection(array_map(function ($item) use ($contactData) {
                $item['external_userid'] = $contactData['external_userid'];
                $item['unionid'] = $contactData['unionid'];

                return $this->syncFollowUserData($item);
            }, $data['follow_user']));

            return $contact;
        });
    }

    /**
     * 根据外部联系人同步数据
     * @param string $externalUserid
     * @return WechatWorkContact|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncByExternalUserid($externalUserid)
    {
        $result = $this->getOfQy($externalUserid);
        if (!$result) {
            return null;
        }

        return $this->syncOfRawData($result);
    }

    /**
     * 通过一组externalUserid数据同步数据到数据库中
     * @param array $externalUseridList
     * @return Collection|\Illuminate\Support\Collection
     */
    public function syncOfExternalUseridList($externalUseridList)
    {
        return (new Collection($externalUseridList))->map(function ($externalUserid) {
            return $this->syncByExternalUserid($externalUserid);
        });
    }

    /**
     * 同步外部联系人
     * @return Collection
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncAll()
    {
        $result = new Collection();

        $userList = $this->user()->getList();
        foreach ($userList as $user) {
            $list = $this->getExternalUseridListOfQy($user['userid']);
            if ($list == null) {
                continue;
            }

            foreach ($list as $externalUserid) {
                $contact = $this->syncByExternalUserid($externalUserid);
                if ($contact) {
                    $result->push($contact);
                }
            }
        }

        return $result;
    }

    /**
     * 获取通讯录服务
     * @return UserService
     */
    public function user()
    {
        if (!$this->user) {
            $this->user = new UserService($this->getWechatFactory());
        }

        return $this->user;
    }

    /**
     * 获取外部联系人信息（企微）
     * @param string $externalUserid
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getOfQy($externalUserid)
    {
        $result = $this->work()->external_contact->get($externalUserid);
        if ($result['errcode'] != 0) {
            return null;
        }

        return Arr::except($result, ['errcode', 'errmsg']);
    }

    /**
     * 获取外部联系人列表（企微）
     * @param string $userid
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getExternalUseridListOfQy($userid)
    {
        $result = WechatResult::make($this->work()->external_contact->list($userid));

        return $result->isSucceeded() ? $result['external_userid'] : null;
    }

    /**
     * 获取所有外部联系人列表（企微）
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAllExternalUseridOfQy()
    {
        $result = [];

        $userList = $this->user()->getList();
        foreach ($userList as $user) {
            $list = $this->getExternalUseridListOfQy($user['userid']);
            if ($list == null) {
                continue;
            }

            $result = array_merge($result, $list);
        }

        return array_unique($result);
    }
}
