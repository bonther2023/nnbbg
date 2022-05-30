<?php

namespace App\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class SettleQueue extends Queue
{
    use Singleton;
}
