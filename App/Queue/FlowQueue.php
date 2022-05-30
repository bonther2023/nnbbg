<?php

namespace App\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class FlowQueue extends Queue
{
    use Singleton;
}
