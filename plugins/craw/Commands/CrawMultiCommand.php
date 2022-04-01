<?php

namespace Plugins\craw\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class CrawMultiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'craw:multi
                            {action* : 要执行的命令：content,site}
                            {--num=10 : 进程数量}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '多进程爬取器';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $commandArgs = $this->argument('action');
        $processNum = $this->option('num');

        $command = array_shift($commandArgs);
        $commandArgsStr = implode(" ", $commandArgs);
        $this->info("start action \"{$command} {$commandArgsStr}\" at " . now());

        $processList = $this->createProcessList($command, $commandArgs, $processNum);

        $this->keep($processList);

        $this->info('end... at ' . now());

        return 0;
    }

    /**
     * @param string $command
     * @param array $commandArgs
     * @param int $num
     * @return Process[]
     */
    protected function createProcessList($command, $commandArgs, $num)
    {

        $processList = [];

        for ($i = 0; $i < $num; $i++) {
            $processList[] = $process = new Process(array_merge([
                'php', 'artisan', 'craw:' . $command,
                // "--only",
            ], $commandArgs), getcwd());

            $process->start();

            $this->info("start process $i , PID: {$process->getPid()} at " . now());
        }

        return $processList;
    }

    /**
     * 保持进程运行
     * @param Process[] $processList
     * @return void
     */
    protected function keep($processList)
    {
        while (count($processList)) {
            $processList = $this->childProcessOutputContent($processList);
            usleep(1000);
        }
    }

    /**
     * 输出子进程打印信息
     * @param Process[] $processList
     * @return Process[]
     */
    protected function childProcessOutputContent($processList)
    {
        foreach ($processList as $key => $process) {
            if (!$process->isRunning()) {
                unset($processList[$key]);
                $this->error("process (PID:{$process->getPid()}) is exist , code {$process->getExitCodeText()}. at " . now());
                continue;
            }

            $output = $process->getOutput();
            if ($output) {
                $process->clearOutput();
                echo "process {$key} (PID:{$process->getPid()}) at " . now() . " :\n", $output, "\n";
            }
        }

        return $processList;
    }
}
