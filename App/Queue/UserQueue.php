<?php

namespace App\Queue;

use EasySwoole\Component\Singleton;
use EasySwoole\Queue\Queue;

class UserQueue extends Queue
{
    use Singleton;
}
