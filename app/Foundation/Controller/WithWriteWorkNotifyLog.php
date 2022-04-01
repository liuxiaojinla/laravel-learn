<?php

namespace App\Foundation\Controller;

use Illuminate\Support\Facades\DB;

use function now;

trait WithWriteWorkNotifyLog
{
    /**
     * 写入数据库日志
     * @param array $message
     * @param string $source
     */
    protected function writeLog(array $message, $source)
    {
        if (empty($message)) {
            return;
        }

        $corpId = $message['ToUserName'] ?? $message['AuthCorpId'] ?? $message['SuiteId'] ?? '';
        $event = $message['Event'] ?? $message['InfoType'] ?? 'message';
        $changeType = $message['ChangeType'] ?? '';

        DB::table('wechat_work_notifys')->insert([
            'corp_id' => $corpId,
            'event' => $event,
            'source' => $source,
            'change_type' => $changeType,
            'query' => http_build_query(request()->query()),
            'data' => json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => now(),
        ]);
    }
}
