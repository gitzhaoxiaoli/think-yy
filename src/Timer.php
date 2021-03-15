<?php
declare (strict_types=1);

namespace yy;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class Timer extends Command
{
    //定时器配置
    protected $timerList = [
        //主动查询订单
        [
            //'time'     => '16:00:00',                 //运行的时间，空为随便
            'interval' => 10 * 60,                       //间隔的秒数
            'class'    => \app\model\Timer::class,      //类
            'method'   => 'payOrderQuery'               //方法
        ],
        //订单超时定时查询，超过5分钟未支付发通知
        [
            //'time'     => '16:00:00',                 //运行的时间，空为随便
            'interval' => 1 * 60,                       //间隔的秒数
            'class'    => \app\model\Timer::class,      //类
            'method'   => 'orderTimeout'                //方法
        ],
        //退款查询
        [
            //'time'     => '16:00:00',                 //运行的时间，空为随便
            'interval' => 10 * 60,                       //间隔的秒数
            'class'    => \app\model\Timer::class,      //类
            'method'   => 'refundOrderQuery'                //方法
        ],
    ];

    //定时器执行缓存
    protected $temp = [];

    protected function configure()
    {
        // 指令配置
        $this->setName('Timer')->setDescription('定时器');
    }

    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        //$output->writeln('Timer');
        cli_set_process_title("====php定时器====");
        $this -> start();
    }

    /**
     * 开始运行
     */
    protected function start()
    {
        while (true) {
            $time = time();
            //遍历配置
            foreach ($this->timerList as $i => $t) {

                if( empty($this->temp[$i]) || $time-$this->temp[$i]>=$t['interval'] ){
                    if( empty($t['time']) || strtotime(date('Y-m-d')." ".$t['time']) >= $time ){
                        echo "\n============================================================\n";
                        echo date("Y-m-d H:i:s")."  {$t['class']}::{$t['method']}";
                        echo "\n";
                        //执行方法
                        $obj = new $t['class'];
                        call_user_func([$obj,$t['method']]);

                        $obj = null;                //销毁
                        $this->temp[$i] = $time;
                        echo "============================================================\n";
                    }
                }
                $this -> loading();
            }
            //每秒执行一次
            sleep(1);
        }
    }

    function __destruct(){
        echo "析构方法执行,5秒钟后重启命令行";
        //重启命令行
        sleep(10);
        pclose(popen('start php think Timer', "r"));
        pclose(popen('exit', "r"));
    }

    protected function loading(){
        $this->loadStrIndex = $this->loadStrIndex ?? 0;
        if( $this->loadStrIndex >= 60 ){
            $this->loadStrIndex = 0;
        };
        $this->loadStrIndex ++;
        echo "\r";
        echo "定时器运行中，当前时间：".date("Y-m-d H:i:s");
    }
}
