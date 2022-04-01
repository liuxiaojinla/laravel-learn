<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkUser;
use App\Services\Wechat\WechatResult;
use Illuminate\Database\Eloquent\Collection;

class UserService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkUser::class;

    /**
     * @var DepartmentService
     */
    protected $department;

    /**
     * 根据ID获取员工信息
     * @param int $id
     * @return WechatWorkUser
     */
    public function getById($id)
    {
        return with($this->dbQuery()->where('id', $id)->first());
    }

    /**
     * 根据ID获取openid
     * @param int $id
     * @return string
     */
    public function getOpenidById($id)
    {
        return $this->dbQuery()->where('id', $id)->value('openid');
    }

    /**
     * 根据userid获取openid
     * @param string $userid
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOpenidByUserid($userid)
    {
        $openid = $this->dbQuery()->where('userid', $userid)->value('openid');
        if (!$openid) {
            $user = $this->syncByUserid($userid);
            $openid = $user ? $user->openid : null;
        }

        return $openid;
    }

    /**
     * 获取员工列表数据
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
     * 删除用户
     * @param string $userid
     * @return false
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteByUserid($userid)
    {
        if (!$this->deleteOfQy($userid)) {
            return false;
        }

        return $this->deleteByUseridOnlyLocal($userid);
    }

    /**
     * 仅删除本地用户数据
     * @param string $userid
     * @return bool
     */
    public function deleteByUseridOnlyLocal($userid)
    {
        $this->dbQuery()->where('userid', $userid)->delete();

        return true;
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkUser
     */
    public function syncOfRawData($data, $openid, $userid)
    {
        return WechatWorkUser::unguarded(function () use ($data, $openid, $userid) {
            $newUserid = $data['new_userid'] ?? '';

            $data = WechatWorkUser::getAllowFields($data);
            $data['corp_id'] = $this->corpId();
            $data['deleted_at'] = null;

            if ($openid) {
                $data['openid'] = $openid;
            }

            if ($newUserid) {
                $data['userid'] = $newUserid;
            }

            $syncBatchNo = $this->getSyncBatchNo();
            if ($syncBatchNo) {
                $data['sync_batch_no'] = $syncBatchNo;
            }

            $info = $this->getLocalByOpenidOrUserid($openid, $userid);

            if ($info) {
                $info->fill($data)->save();
            } else {
                $data = array_merge([
                    'name' => '',
                    'isleader' => 0,
                    'enable' => 1,
                    'order' => [],
                ], $data);
                $info = $this->dbQuery()->create($data);
            }

            return $info;
        });
    }

    /**
     * 尝试从本地根据openid或userid读取数据
     * @param string $openid
     * @param string $userid
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    protected function getLocalByOpenidOrUserid($openid, $userid)
    {
        $info = null;

        if ($openid) {
            $info = $this->dbWithTrashedQuery()->where('openid', $openid)->first();
        }

        if (!$info) {
            $info = $this->dbWithTrashedQuery()->where('userid', $userid)->first();
        }

        return $info;
    }

    /**
     * 同步一组数据到数据库中
     * @param array $users
     * @return Collection|\Illuminate\Support\Collection
     */
    public function syncOfRawItems($users)
    {
        return (new Collection($users))->map(function ($user) {
            $openid = $this->getOpenidByUseridOfQy($user['userid']);
            if ($openid) {
                $user['openid'] = $openid;
            }

            return $this->syncOfRawData($user, $openid, $user['userid']);
        });
    }

    /**
     * 根据企微userid同步数据
     * @param string $userid
     * @return WechatWorkUser
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncByUserid($userid)
    {
        $result = $this->getOfQy($userid);
        if (!$result) {
            return null;
        }

        $openid = $this->getOpenidByUseridOfQy($userid);
        if ($openid) {
            $result['openid'] = $openid;
        }

        return $this->syncOfRawData($result, $openid, $userid);
    }

    /**
     * 根据企微openid同步数据
     * @param string $openid
     * @return WechatWorkUser
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncByOpenid($openid)
    {
        $userid = $this->getUseridByOpenidOfQy($openid);
        if (!$userid) {
            return null;
        }

        $result = $this->getOfQy($userid);
        if (!$result) {
            return null;
        }

        $result['openid'] = $openid;

        return $this->syncOfRawData($result, $openid, $userid);
    }

    /**
     * 根据ID同步数据
     * @param int $id
     * @return WechatWorkUser
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function syncById($id)
    {
        $info = $this->getById($id);
        if (!$info) {
            return null;
        }

        return $info['openid'] ? $this->syncByOpenid($info['openid']) : $this->syncByUserid($info['userid']);
    }

    /**
     * 全量同步数据
     * @param bool $department
     * @return Collection|\Illuminate\Support\Collection
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncAll($department = true)
    {
        $this->startBatch();

        $users = $this->getAllOfQy($department);
        $users = $this->syncOfRawItems($users);

        $this->completeBatch(function ($batchNo) {
            $this->dbQuery()->where('sync_batch_no', '<>', $batchNo)->delete();
        });

        return $users->filter();
    }

    /**
     * 获取通讯录服务
     * @return DepartmentService
     */
    public function department()
    {
        if (!$this->department) {
            $this->department = new DepartmentService($this->getWechatFactory());
        }

        return $this->department;
    }

    /**
     * 获取员工信息（企微）
     * @param string $userid
     * @return array|null
     */
    public function getOfQy($userid)
    {
        $result = WechatResult::capture(function () use ($userid) {
            return $this->work()->user->get($userid);
        });

        return $result->isSucceeded() ? $result->toArray() : null;
    }

    /**
     * 获取所有员工列表（企微）
     * @param bool $department
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAllOfQy($department = true)
    {
        if ($department) {
            $users = [];
            $departments = $this->department()->getListOfQy();
            foreach ($departments as $department) {
                $result = $this->getDepartmentUsersOfQy($department['id'], true);
                $users = array_merge($users, $result);
            }
        } else {
            $users = $this->getDepartmentUsersOfQy(1, true);
        }

        return collect($users)->unique('userid')->toArray();
    }

    /**
     * 获取部门下的员工列表（企微）
     * @param int $departmentId
     * @param bool $fetchChild
     * @return mixed|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getDepartmentUsersOfQy(int $departmentId, bool $fetchChild = false)
    {
        $result = WechatResult::make($this->work()->user->getDetailedDepartmentUsers($departmentId, $fetchChild));

        return $result->isSucceeded() ? $result['userlist'] : [];
    }

    /**
     * 获取员工openid（企微）
     * @param string $userid
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOpenidByUseridOfQy($userid)
    {
        $result = WechatResult::make($this->work()->user->userIdToOpenid($userid));

        return $result->isSucceeded() ? $result['openid'] : null;
    }

    /**
     * 获取员工userid（企微）
     * @param string $openid
     * @return string
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getUseridByOpenidOfQy($openid)
    {
        $result = WechatResult::make($this->work()->user->openidToUserId($openid));

        return $result->isSucceeded() ? $result['userid'] : null;
    }

    /**
     * 删除通讯录成员（企微）
     * @param string $userid
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteOfQy($userid)
    {
        $result = WechatResult::make($this->work()->user->delete($userid));

        return $result->isSucceeded();
    }
}
