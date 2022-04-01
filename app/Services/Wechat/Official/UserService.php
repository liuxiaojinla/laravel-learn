<?php

namespace App\Services\Wechat\Official;

use App\Models\Wechat\Officials\User;
use App\Services\Wechat\BaseService;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;

class UserService extends BaseService
{
    /**
     * @var string
     */
    protected $model = User::class;

    /**
     * 根据ID获取粉丝信息
     * @param int $id
     * @return User
     */
    public function getById($id)
    {
        return with($this->dbQuery()->where('id', $id)->first());
    }

    /**
     * 根据ID获取粉丝openid
     * @param int $id
     * @return string
     */
    public function getOpenidById($id)
    {
        return $this->dbQuery()->where('id', $id)->value('openid');
    }

    /**
     * 根据粉丝openid获取unionid
     * @param string $openid
     * @return string
     */
    public function getUnionidByOpenid($openid)
    {
        $unionid = $this->dbQuery()->where('openid', $openid)->value('unionid');
        if (!$unionid) {
            $user = $this->syncByOpenid($unionid);
            $unionid = $user ? $user->unionid : null;
        }

        return $unionid;
    }

    /**
     * 获取粉丝列表数据
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getList()
    {
        $data = $this->dbQuery()->get();
        if ($data->isEmpty()) {
            return $this->syncAll();
        }

        return $data;
    }

    /**
     * 仅删除本地粉丝数据
     * @param string $userid
     * @return bool
     */
    public function deleteByOpenidOnlyLocal($userid)
    {
        $this->dbQuery()->where('openid', $userid)->delete();

        return true;
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return User
     */
    public function syncOfRawData($data)
    {
        return User::unguarded(function () use ($data) {
            $data = User::getAllowFields($data);
            $data['corp_id'] = $this->appId();
            $data['deleted_at'] = null;

            $syncBatchNo = $this->getSyncBatchNo();
            if ($syncBatchNo) {
                $data['sync_batch_no'] = $syncBatchNo;
            }

            return $this->dbWithTrashedQuery()->updateOrCreate([
                'openid' => $data['openid'],
            ], $data);
        });
    }

    /**
     * 同步一组数据到数据库中
     * @param array $users
     * @return Collection|\Illuminate\Support\Collection
     */
    public function syncOfRawItems($users)
    {
        return (new Collection($users))->map(function ($user) {
            return $this->syncOfRawData($user);
        });
    }

    /**
     * 根据粉丝openid同步数据
     * @param string $openid
     * @return User
     */
    public function syncByOpenid($openid)
    {
        $result = $this->getOfWechat($openid);
        if (!$result) {
            return null;
        }

        return $this->syncOfRawData($result);
    }

    /**
     * 根据粉丝openids同步数据
     * @param array $openidList
     * @return User
     */
    public function syncByOpenidList($openidList)
    {
        $result = $this->getListByOpenidListOfWechat($openidList);
        if (!$result) {
            return null;
        }

        return $this->syncOfRawData($result);
    }

    /**
     * 根据ID同步数据
     * @param int $id
     * @return User
     */
    public function syncById($id)
    {
        $info = $this->getById($id);
        if (!$info) {
            return null;
        }

        return $this->syncByOpenid($info['openid']);
    }

    /**
     * 全量同步数据
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncAll()
    {
        $this->startBatch();

        $result = [];
        $openidList = $this->getAllOpenidOfWechat();
        foreach (array_chunk($openidList, 10000) as $openids) {
            $users = $this->syncByOpenidList($openids);
            $result = array_merge($result, $users);
        }

        $this->completeBatch(function ($batchNo) {
            $this->dbQuery()->where('sync_batch_no', '<>', $batchNo)->delete();
        });

        return $result;
    }

    /**
     * 获取粉丝信息（微信）
     * @param string $openid
     * @return array|null
     */
    public function getOfWechat($openid)
    {
        $result = WechatResult::capture(function () use ($openid) {
            return $this->work()->user->get($openid);
        });

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 批量获取粉丝信息（微信）
     * @param array $openidList
     * @return array|null
     */
    public function getListByOpenidListOfWechat($openidList)
    {
        $result = WechatResult::capture(function () use ($openidList) {
            return $this->official()->user->select($openidList);
        });

        return $result->isSucceeded() ? $result['user_info_list'] : null;
    }

    /**
     * 获取所有粉丝openid列表（微信）
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getOpenidListOfWechat($nextOpenid = null)
    {
        $result = WechatResult::make($this->official()->user->list($nextOpenid));

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 获取所有粉丝openid列表（微信）
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAllOpenidOfWechat()
    {
        $result = [];
        $nextOpenid = null;

        while (true) {
            $list = $this->getOpenidListOfWechat($nextOpenid);

            $result = array_merge($result, $list['openid']);

            $nextOpenid = $list['next_openid'] ?? null;
            if (!$nextOpenid) {
                break;
            }
        }

        return $result;
    }

    /**
     * 获取粉丝unionid（微信）
     * @param string $openid
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getUnionidByOpenidOfWechat($openid)
    {
        $result = WechatResult::make($this->official()->user->get($openid));

        return $result->isSucceeded() ? $result['unionid'] ?? null : null;
    }
}
