<?php

namespace App\Http\Controllers;

use App\Contracts\Bot\Factory as BotFactory;
use App\Jobs\TestJob;
use App\Services\Wechat\Official\UserService;
use App\Services\Wework\WeworkManager;
use App\Support\Facades\Bot;
use Illuminate\Support\Facades\Bus;

class HomeController extends Controller
{
    protected $a = [
        'hello world',
        'hello world',
        'hello world',
        'hello world',
    ];
    /**
     * @var int
     */
    private $aa;

    /**
     * @var int
     */
    private $sa;

    /**
     * @param UserService $userService
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function index(UserService $userService)
    {
        // $pipeline = new Pipeline(
        //     new MiddlewareProcessor(function ($payload) {
        //         return 'hello :' . $payload['name'] ?? '';
        //     })
        // );
        // $response = $pipeline->pipe(function ($payload, $next) {
        //     $payload['name'] = '小明';
        //
        //     return $next($payload);
        // })->pipe(function ($payload, $next) {
        //     $response = $next($payload);
        //
        //     return "【{$response}】";
        // })->process([]);
        // dd('szzzzss', $response);

        // $canvas = new Canvas(500, 500);
        // $canvas->setBackgroundColor(new Color(0, 0, 0, 127));
        // $canvas->draw(new Rectangle(
        //     new Dimension(200, 200),
        //     new Position(0, 10),
        //     new Color(255),
        //     new Color(0, 255)
        // ));
        //
        // $path = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHoAAAAcCAMAAACZKluRAAAAmVBMVEUAAAD////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VHQRUAAAAMnRSTlMA+2+MeTBUD6jAthvbFvFT7dWxE+OdagOGu0zGrIBE9yML3mLNQOeVJyCjkXJXPFo3a4O09IAAAAJuSURBVEjHxdXbeqIwFIbhhcKICIoKgoriBtxrO//9X9wkRiYkCDN6YL8Dmjzp07eFVSRWP1t49DMB6A/oRwJvSR9O0sGaraLcenrrA5Nfk75SJM/PjkV1jVBppNKI2arDvvqnGekZuLKrA6USHcI31b5eoYeC5mV5hQ45bSgptF73FdqICpqtTZIFQQCkQbCnmprpWavSTKPREbSo5OBR0kjvHKU2NdVIo0VFSZIAQZJcVpaSesNt0nqfNg7Ks7arT21NvPFmszkwuu8q2a/S6yCtzkL3951etp7TbGWF0Ju/SLMi6y+wKo7bd9oNF1NWl50Mp7z/on/VpNMiEyJTpX0xeR47mYgDSVsDJVvSqEmnH03FfqrRbfoOw0jQ2/BSpklt8TZ9FXtXoxfsR27EX81WQQMdA9336KPYpxptkgP7Qc8Ar5aOMuBMoh7r4APznloNbYm9r9ET2sEUNL/OJN1ulTMzsFP1/uNGajX0VuyHGr3sActizAIsJF0tIVkvLe1v1+ugnl5vxH6k0asBMC7oEUYNtN0h2QkwVsVmxk63dfTahmir0rsoZlNW0GYxZ52unhMvqdQ3gLPy8e2PddpasW6XDUSpp9IZn7KClnP2z+ZAfyy3xxQINVrvTCp9Ih9mQcs5O8YstjTbaubjN/uSLyf5xsobad/T6MuRT1k8F3QxZ50h27tjimtepeuAnw9LuXyCowbakM8Lsfj4GPBx2IE1oGLOIodt/QNtoST/N3M8La+njdIUuoc77cVw72+b9BTd79uOXff8e3O6pVV5K6Zwjidl41raX9Jngpax9+gD6XR/6Ex69JH+AHkv4TA4VEfWAAAAAElFTkSuQmCC';
        // $image = Image::fromPath($path);
        // $image->setPosition(new Position(100, 100));
        // $canvas->draw($image);
        // $canvas->save('aa1.png');

        // Settings::setCache(
        //     (new CacheManager(app()))->driver()
        // );

        // $result = range(0, 110000);
        // foreach (range(0, 100000) as $i) {
        //     $result[$i] = [
        //         'id' => $i,
        //         'title' => Str::random(10),
        //     ];
        // }
        // $writer = new Writer();
        // $writer->write(new TableExport([
        //     Column::create('id', 'ID'),
        //     Column::create('title', '标题'),
        // ], $result), '00.xlsx', 'Xlsx');

        // $reader = new Reader();
        // $reader->read(new TableImport([
        //
        // ], function (Row $row) {
        // }), '00.xlsx', 'Xlsx');

        // LimitManager::named('qyfriend', new QyFriendLimiter());
        // LimitManager::named('location', new LocationLimiter(true));
        // LimitManager::named('sub', new RegionLimiter());

        // $limiter = new LimitManager();
        // $data = $limiter->useAll()->process([]);
        // dd($data);

        // $value = DB::table('aaa')->orderByDesc('id')->value('content');
        // for ($i = 0;$i < 1000000;$i++) {
        //     DB::table('aaa')->insert([
        //         'content' => $value,
        //     ]);
        // }
        // $data = file_get_contents('https://ask.csdn.net/questions/995174');
        // $a = gzcompress($data);
        // DB::table('aaa')->insert([
        //     'content' => $a,
        // ]);
        // $value = DB::table('aaa')->orderByDesc('id')->value('content');
        // dd($value);
        // $this->aa = memory_get_usage();
        // $this->aa($this->a);

        // $userService->syncAll();

        // SyncDepartments::checkForDispatch();
        // SyncUsers::checkForDispatch();

        // SyncContactTags::checkForDispatch();
        // SyncContacts::checkForDispatch();
        // SyncContactTags::dispatchNow(Promise::make('sync_contact_tags'));

        // SyncGroupChats::checkForDispatch();

        // dispatch(function () use ($wayService) {
        //     $wayService->syncAll();
        // })->onQueue('wechat_work');
        // $process = new Process(['C:\Program Files (x86)\Tencent\WeChat\WeChat.exe']);
        // $process->start();

        return $this->result(
            $this->urls()
        );
    }

    public function bot(BotFactory $bot)
    {
        Bot::sendTextMessage('hello world');
        $bot->bot('default')->sendTextMessage('hello world');
        $bot->bot('primary')->sendTextMessage('hello world');
    }

    /**
     * @throws \Throwable
     */
    public function batch()
    {
        Bus::batch([
            new TestJob(),
            new TestJob(),
            new TestJob(),
            new TestJob(),
            new TestJob(),
        ])->then(function () {
            echo 'bus batch success.';
        })->catch(function () {
            echo 'bus batch catch.';
        })->finally(function () {
            echo 'bus finally.';
        })->dispatch();
    }

    /**
     * @return mixed
     */
    public function showSuccess()
    {
        return $this->success('业务成功！', $this->urls());
    }

    /**
     * @return mixed
     */
    public function showError()
    {
        return $this->error('业务失败！', null, $this->urls());
    }

    protected function urls(): array
    {
        return [
            [
                'title' => '业务结果',
                'url' => url('/api'),
            ],
            [
                'title' => '业务成功',
                'url' => url('/api/success'),
            ],
            [
                'title' => '业务结果',
                'url' => url('/api/error'),
            ],
        ];
    }

    private function aa(array $a)
    {
        // $a[] = 'ssss';
        // $a[] = 'ssss';
        // $a[] = 'ssss';
        // $a[] = 'ssss';
        // $a[] = 'ssss';

        $b = [];
        $b = [1];

        dump($this->a, $a, memory_get_usage() - $this->aa);
        dd($this->a === $a);

        $b = ['sssssssssss'];
    }
}
