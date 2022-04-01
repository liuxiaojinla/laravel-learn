<?php

namespace App\Services\Wechat\Work;

use App\Models\Wechat\WechatWorkDepartment;
use App\Services\Wechat\WechatResult;

class DepartmentService extends WorkBaseService
{
    /**
     * @var string
     */
    protected $model = WechatWorkDepartment::class;

    /**
     * 根据ID获取部门信息
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getById($id)
    {
        return $this->dbQuery()->where('id', $id)->first();
    }

    /**
     * 根据企微部门ID获取部门信息
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByDepartmentId($departmentId)
    {
        return $this->dbQuery()->where('department_id', $departmentId)->first();
    }

    /**
     * 删除部门
     * @param string $userid
     * @return false
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteByDepartmentId($userid)
    {
        if (!$this->deleteOfQy($userid)) {
            return false;
        }

        return $this->deleteByDepartmentIdOnlyLocal($userid);
    }

    /**
     * 仅删除本地部门数据
     * @param string $userid
     * @return bool
     */
    public function deleteByDepartmentIdOnlyLocal($userid)
    {
        $this->dbQuery()->where('userid', $userid)->delete();

        return true;
    }

    /**
     * 同步数据到数据库中
     * @param array $data
     * @return WechatWorkDepartment
     */
    public function syncOfRawData($data)
    {
        return WechatWorkDepartment::unguarded(function () use ($data) {
            $data = WechatWorkDepartment::getAllowFields($data);
            $data['corp_id'] = $this->corpId();

            return $this->dbQuery()->updateOrCreate([
                'department_id' => $data['department_id'],
            ], $data);
        });
    }

    /**
     * 同步部门信息
     * @return WechatWorkDepartment[]|array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function syncAll()
    {
        $list = $this->getListOfQy();

        return array_map(function ($item) {
            $item['department_id'] = $item['id'];
            unset($item['id']);

            return $this->syncOfRawData($item);
        }, $list);
    }

    /**
     * 获取部门列表
     * @return array|null
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getListOfQy()
    {
        $result = WechatResult::make($this->work()->department->list());

        return $result->isSucceeded() ? $result['department'] : null;
    }

    /**
     * 删除通讯录部门（企微）
     * @param string $id
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function deleteOfQy($id)
    {
        $result = WechatResult::make($this->work()->department->delete($id));

        return $result->isSucceeded();
    }
}
