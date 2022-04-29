<?php

namespace App\Events\Corp;

class AppUsePeopleEvent
{
    /**
     * 添加使用人员
     */
    public const ADD = "add";

    /**
     * 移除使用人员
     */
    public const REMOVE = "remove";

    /**
     * @var string
     */
    public string $corpId;

    /**
     * @var string
     */
    public string $userid;

    /**
     * @var string|null
     */
    public ?string $adminUserid;

    /**
     * @var string
     */
    public string $type;

    /**
     * @param string $type
     * @param string $corpId
     * @param string $userid
     * @param string|null $adminUserid
     */
    public function __construct(string $type, string $corpId, string $userid, string $adminUserid = null)
    {
        $this->type = $type;
        $this->corpId = $corpId;
        $this->userid = $userid;
        $this->adminUserid = $adminUserid;
    }

    /**
     * @return string
     */
    public function getCorpId(): string
    {
        return $this->corpId;
    }

    /**
     * @return string
     */
    public function getUserid(): string
    {
        return $this->userid;
    }

    /**
     * @return string|null
     */
    public function getAdminUserid(): ?string
    {
        return $this->adminUserid;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
