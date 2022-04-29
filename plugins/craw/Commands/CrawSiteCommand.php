<?php

namespace Plugins\craw\Commands;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Plugins\craw\Models\CrawSite;
use QL\QueryList;

class CrawSiteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'craw:site';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '站点信息读取器';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('doing...' . now());

        while (true) {
            $site = CrawSite::popNotReadSite();
            if ($site) {
                $this->readInfo($site);
            } else {
                $this->comment('ping...' . now());
                sleep(1);
            }
        }

        $this->info('done...' . now());

        return 0;
    }

    /**
     * 读取信息
     * @param CrawSite $site
     * @return void
     */
    protected function readInfo(CrawSite $site)
    {
        try {
            $this->info("site \"{$site->host}\" reading at " . now());
            $query = QueryList::get("//{$site->host}", [], [
                'timeout' => 5,
            ]);
            $this->update($site, $query);
        } catch (RequestException $requestException) {
            echo '读取失败：响应异常：' . $requestException->getRequest()->getUri(), "\n";
            $site->reduceWeight(5, $requestException->getMessage());
        } catch (ConnectException $connectException) {
            echo '读取失败：连接异常：' . $connectException->getRequest()->getUri(), "\n";
            $site->reduceWeight(10, $connectException->getMessage());
        } catch (\Exception $e) {
            echo '读取失败：操作异常：', $site->host, $e->getMessage(), "\n";
            $site->reduceWeight(100, $e->getMessage());
        }
    }

    /**
     * 更新读取信息
     * @param CrawSite $site
     * @param QueryList $query
     * @return void
     */
    protected function update(CrawSite $site, QueryList $query)
    {
        $title = $query->find('title')->text();

        $site->raiseWeight(1, CrawSite::checkEncoding($title) ? $title : '');

        $this->info("site \"{$site->host}\" read at " . now());

        if (CrawSite::checkDenyWithTitle($title) || CrawSite::checkDenyWithHost($site->host)) {
            $site->setDeny();

            $this->error("The site \"{$site->host}\" failed the verification and has been disabled at " . now());
        }
    }
}
