<?php

namespace App\Http\Controllers\Notify;

use App\Foundation\Controller\WithWriteWorkNotifyLog;
use App\Services\Wechat\Work\Events\ContactChange;
use App\Services\Wechat\Work\Events\ExternalContactAdd;
use App\Services\Wechat\Work\Events\ExternalContactChange;
use App\Services\Wechat\Work\Events\ExternalContactDelete;
use App\Services\Wechat\Work\Events\ExternalContactEdit;
use App\Services\Wechat\Work\Events\ExternalContactFollowUserDelete;
use App\Services\Wechat\Work\Events\ExternalContactTransferFail;
use App\Services\Wechat\Work\Events\ExternalGroupChatChange;
use App\Services\Wechat\Work\Events\ExternalGroupChatCreate;
use App\Services\Wechat\Work\Events\ExternalGroupChatDismiss;
use App\Services\Wechat\Work\Events\ExternalGroupChatUpdate;
use App\Services\Wechat\Work\Events\ExternalTagChange;
use App\Services\Wechat\Work\Events\ExternalTagCreate;
use App\Services\Wechat\Work\Events\ExternalTagDelete;
use App\Services\Wechat\Work\Events\ExternalTagShuffle;
use App\Services\Wechat\Work\Events\ExternalTagUpdate;
use App\Services\Wechat\Work\Events\PartyChange;
use App\Services\Wechat\Work\Events\PartyCreate;
use App\Services\Wechat\Work\Events\PartyDelete;
use App\Services\Wechat\Work\Events\PartyUpdate;
use App\Services\Wechat\Work\Events\TagUpdate;
use App\Services\Wechat\Work\Events\UserChange;
use App\Services\Wechat\Work\Events\UserCreate;
use App\Services\Wechat\Work\Events\UserDelete;
use App\Services\Wechat\Work\Events\UserUpdate;
use App\Support\WeWork;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class WechatWorkController
{
    use WithWriteWorkNotifyLog;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EasyWeChat\Kernel\Exceptions\BadRequestException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \ReflectionException
     */
    public function __invoke($corpId = null, $agentId = null)
    {
        if ($corpId && $agentId) {
            $work = WeWork::workByCorpId($corpId, $agentId);
        } else {
            $work = WeWork::work();
        }

        Log::info('work', [http_build_query(Request::query())]);
        $work->server->push(function ($message) {
            Log::info('work-parse-msg', [$message]);
            // 验证去重
            if ($this->isReduplicate($message)) {
                return;
            }

            // 写入日志
            $this->writeLog($message, 'work');

            switch ($message['Event']) {
                case 'change_contact': // 成员通知事件
                    $this->dispatchContactEvent($message);

                    break;
                // 数据格式参考(https://work.weixin.qq.com/api/doc/90001/90143/92277)
                case 'change_external_contact': // 外部联系人事件
                    $this->dispatchExternalContactEvent($message);

                    break;
                case 'change_external_chat': // 外部联系人群事件
                    $this->dispatchExternalChatEvent($message);

                    break;
                case 'change_external_tag': // 标签变更
                    $this->dispatchExternalTagEvent($message);

                    break;
                default:
                    break;
            }

            switch ($message['Event']) {
                case 'subscribe': // 应用可见
                    break;
                case 'unsubscribe': // 应用不可见
                    break;
            }
        });

        return $work->server->serve();
    }

    /**
     * 分配事件
     * @param array $message
     */
    protected function dispatchContactEvent($message)
    {
        event(new ContactChange($message));
        switch ($message['ChangeType']) {
            case ContactChange::CREATE_USER: // 新增成员事件
                event(new UserChange($message));
                event(new UserCreate($message));

                break;
            case ContactChange::UPDATE_USER: // 更新成员事件
                event(new UserChange($message));
                event(new UserUpdate($message));

                break;
            case ContactChange::DELETE_USER: //  删除成员事件
                event(new UserChange($message));
                event(new UserDelete($message));

                break;
            case ContactChange::CREATE_PARTY: //  新增部门事件
                event(new PartyChange($message));
                event(new PartyCreate($message));

                break;
            case ContactChange::UPDATE_PARTY: //  更新部门事件
                event(new PartyChange($message));
                event(new PartyUpdate($message));

                break;
            case ContactChange::DELETE_PARTY: //  删除部门事件
                event(new PartyChange($message));
                event(new PartyDelete($message));

                break;
            case ContactChange::UPDATE_TAG: //  标签成员变更事件
                event(new TagUpdate($message));

                break;
        }
    }

    /**
     * 分配外部联系人变更事件
     * @param array $message
     */
    protected function dispatchExternalContactEvent($message)
    {
        event(new ExternalContactChange($message));
        switch ($message['ChangeType']) {
            case ExternalContactChange::ADD_EXTERNAL_CONTACT: //  添加客户事件
            case ExternalContactChange::ADD_HALF_EXTERNAL_CONTACT: // 免验证成员添加客户事件
                event(new ExternalContactAdd($message));

                break;
            case ExternalContactChange::DEL_EXTERNAL_CONTACT: // 删除客户事件（员工删客户）
                event(new ExternalContactDelete($message));

                break;
            case ExternalContactChange::DEL_FOLLOW_USER: // 删除跟进成员事件（客户删员工）
                event(new ExternalContactFollowUserDelete($message));

                break;
            case ExternalContactChange::EDIT_EXTERNAL_CONTACT: //  编辑客户事件
                event(new ExternalContactEdit($message));

                break;
            case ExternalContactChange::TRANSFER_FAIL: //  客户接替失败事件
                event(new ExternalContactTransferFail($message));

                break;
        }
    }

    /**
     * 分配外部群变更事件
     * @param array $message
     */
    protected function dispatchExternalChatEvent($message)
    {
        event(new ExternalGroupChatChange($message));

        switch ($message['ChangeType']) {
            case ExternalGroupChatChange::CREATE: // 客户群创建
                event(new ExternalGroupChatCreate($message));

                break;
            case ExternalGroupChatChange::UPDATE: // 客户群变更
                event(new ExternalGroupChatUpdate($message));

                break;
            case ExternalGroupChatChange::DISMISS: // 客户群解散
                event(new ExternalGroupChatDismiss($message));

                break;
        }
    }

    /**
     * 分配客户标签变更事件
     * @param array $message
     */
    protected function dispatchExternalTagEvent($message)
    {
        event(new ExternalTagChange($message));
        switch ($message['ChangeType']) {
            case ExternalTagChange::CREATE: // 企业客户标签创建事件
                event(new ExternalTagCreate($message));

                break;
            case ExternalTagChange::UPDATE: // 企业客户标签变更事件
                event(new ExternalTagUpdate($message));

                break;
            case ExternalTagChange::DELETE: // 企业客户标签删除事件
                event(new ExternalTagDelete($message));

                break;
            case ExternalTagChange::SHUFFLE: // 企业客户标签重排事件
                event(new ExternalTagShuffle($message));

                break;
        }
    }

    /**
     * 消息是否重叠
     * @param array $message
     * @return bool
     */
    protected function isReduplicate(array $message)
    {
        $key = 'lock:wechat_work:notify:' . md5(json_encode($message));
        $lock = Cache::lock($key, 3);
        if ($lock->get()) {
            return false;
        }

        return true;
    }
}
