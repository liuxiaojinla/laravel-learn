<?php

namespace Plugins\craw\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Plugins\craw\Models\CrawContent;
use QL\QueryList;

class AnalysisContentCommand extends Command
{
    /**
     * @var array
     */
    protected $keywords = [
        'QQ', '微信', 'weixin', 'wechat', '公众号',
        '手机号', 'phone', 'mobile', 'email', '邮箱',
        '支付宝',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analysis:content
                            {--only : 分析数据内容}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '分析数据内容';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CrawContent::query()->where('keywords', '<>', '')->chunk(100, function (Collection $collection) {
            $collection->each([$this, 'itemHandle']);
        });

        return 0;
    }

    /**
     * @param CrawContent $crawContent
     * @return void
     */
    public function itemHandle(CrawContent $crawContent)
    {
        $query = QueryList::html($crawContent->content)->find('body')->remove('script');
        $content = $query->text();

        $result = [];
        foreach ($this->keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $result[] = $keyword;
            }
        }

        $crawContent->keywords = implode(",", $result);
        $crawContent->save();

        $this->comment($crawContent->title . ':' . $crawContent->keywords);
    }
}
