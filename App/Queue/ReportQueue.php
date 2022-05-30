<?php

namespace App\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class ReportQueue extends Queue
{
    use Singleton;
}
