<?php

namespace Neat\Cache;

use InvalidArgumentException as Root;
use Psr\SimpleCache\InvalidArgumentException as Psr;

class InvalidArgumentException extends Root implements Psr
{
}
