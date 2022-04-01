<?php

namespace Plugins\craw\Commands;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Plugins\craw\Models\CrawSite;
use Plugins\craw\Models\CrawUrl;
use QL\QueryList;

class CrawContentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'craw:content
                            {--only : 仅抓取当前的站点}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '内容读取器';

    /**
     * @var CrawUrl
     */
    protected $link;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $only = $this->option('only');

        $this->info('doing...');

        while (true) {
            $link = CrawUrl::pop();
            if (!$link) {
                break;
            }

            if ($only && !$this->link) {
                $this->link = $link;
            }

            $this->readLinks($link);

            $this->refreshExcludeHosts();
        }

        $this->info('done...');

        return 0;
    }

    /**
     * @param CrawUrl $link
     * @return void
     */
    protected function readLinks(CrawUrl $link)
    {
        if (!$link->isActiveUrl()) {
            $link->setReadStatus('cancel');

            return;
        }

        $newUrl = $link->getAttribute('url');

        try {
            $query = QueryList::get($newUrl, [], [
                'timeout' => 5,
            ]);

            $title = $query->find('title')->text();
            if ($link->isAllowWriteContent()) {
                $link->setContent($query->getHtml(), $title);
            } else {
                $link->raiseWeight(1, $title);
            }

            $data = $query->find('a')->attrs('href');
            $data->each(function ($crawUrl) use ($newUrl) {
                $this->putUrl($crawUrl, $newUrl);
            });
        } catch (RequestException $requestException) {
            $this->warn('读取失败：响应失败：' . $requestException->getRequest()->getUri() . $requestException->getMessage());
            $link->setUnReadStatus($requestException->getMessage(), $requestException->getResponse()->getStatusCode() == 404 ? 100 : 5);
        } catch (ConnectException $connectException) {
            $this->warn('读取失败：连接失败：' . $connectException->getRequest()->getUri() . $connectException->getMessage());
            $link->setUnReadStatus($connectException->getMessage(), 10);
        } catch (\Exception $e) {
            $this->warn('读取失败：操作异常：' . $newUrl . " " . $e->getMessage() . $e->getTraceAsString());
            $link->setUnReadStatus($e->getMessage(), 100);
        }

        usleep(rand(50000, 500000));
    }

    /**
     * @param string $url
     * @param string $originUrl
     * @return void
     */
    public function putUrl($url, $originUrl)
    {
        $optimizeUrl = OptimizeURL::parse($url, $originUrl);
        if (!$optimizeUrl || !CrawUrl::checkActiveUrl($optimizeUrl)) {
            return;
        }

        // 检查是否只读取特定的域名
        if ($this->link && $optimizeUrl->host() != $this->link->build()->host()) {
            return;
        }

        $site = CrawSite::put($optimizeUrl->host());
        if ($site->isDeny()) {
            return;
        }

        $this->comment($optimizeUrl->md5());

        CrawUrl::query()->firstOrCreate([
            'md5' => $optimizeUrl->md5(),
        ], [
            'url' => $optimizeUrl->build(),
            'host' => $optimizeUrl->host(),
            'path' => $optimizeUrl->path(),
            'origin' => $originUrl,
            'priority' => rand(0, 100),
        ]);
    }

    protected function refreshExcludeHosts()
    {
        if (rand(0, 5)) {
            CrawSite::refreshExcludeHosts();

            $this->info('refresh exclude hosts...');
        }
    }
}
