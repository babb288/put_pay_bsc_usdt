<?php

namespace app\task\command;


use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;


class Main  extends Command
{


    protected function configure()
    {
        $this->setName('main')
            ->addArgument('name', Argument::OPTIONAL, "程序名")
            ->addOption('time', null, Option::VALUE_REQUIRED, '附加参数')
            ->setDescription('主服务');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        
        if (empty($name)) {
            $output->writeln("错误: 请指定任务名称");
            return;
        }

        // 构建完整的类名
        $className = 'app\\task\\task\\' . $name;
        
        // 检查类是否存在
        if (class_exists($className)) {
            try {
                // 使用容器动态实例化类（支持依赖注入）
                $instance = app($className);
                if (method_exists($instance, 'run')) {
                    $instance->run();
                } else {
                    $output->writeln("错误: 类 '{$className}' 没有 run() 方法");
                }
            } catch (\Exception $e) {
                $output->writeln("错误: " . $e->getMessage());
                $output->writeln("文件: " . $e->getFile());
                $output->writeln("行号: " . $e->getLine());
            } catch (\Error $e) {
                $output->writeln("错误: " . $e->getMessage());
                $output->writeln("文件: " . $e->getFile());
                $output->writeln("行号: " . $e->getLine());
            }
        } else {
            $output->writeln("错误: 任务类 '{$className}' 不存在");
        }

    }

}