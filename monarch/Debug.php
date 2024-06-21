<?php

namespace Monarch;

use Tracy\Debugger;

class Debug
{
    /**
     * Starts Tracy debugger and set its configuration.
     */
    public static function startTracy(): void
    {
        $config = config('tracy');
        $tracy = Debugger::getBar();

        foreach ($config as $key => $value) {
            if ($value === null) {
                continue;
            }

            Debugger::${$key} = $value;
        }

        Debugger::enable(env('DEBUG') ? Debugger::Development : Debugger::Production);
    }
}
