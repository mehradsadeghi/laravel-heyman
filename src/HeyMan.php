<?php

namespace Imanghafoori\HeyMan;

use Illuminate\Support\Arr;
use Imanghafoori\HeyMan\Core\Forget;
use Imanghafoori\HeyMan\Switching\Turn;
use Imanghafoori\HeyMan\Core\Situations;
use Imanghafoori\HeyMan\Core\ConditionsFacade;

class HeyMan
{
    use Turn;

    public function forget()
    {
        return new Forget();
    }

    public function __call($method, $args)
    {
        resolve('heyman.chain')->startChain();

        $this->writeDebugInfo(debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1]);

        return Situations::call($method, $args);
    }

    public function checkPoint(string $pointName)
    {
        event('heyman_checkpoint_'.$pointName);
    }

    public function aliasCondition(string $currentName, string $newName)
    {
        resolve(ConditionsFacade::class)->alias($currentName, $newName);
    }

    public function aliasSituation(string $currentName, string $newName)
    {
        Situations::aliasMethod($currentName, $newName);
    }

    public function defineCondition(string $name, $callable)
    {
        resolve(ConditionsFacade::class)->define($name, $callable);
    }

    public function condition(string $name, $callable)
    {
        $this->defineCondition($name, $callable);
    }

    /**
     * @param $debugTrace
     */
    private function writeDebugInfo($debugTrace)
    {
        if (config('app.debug') && ! app()->environment('production')) {
            $info = Arr::only($debugTrace, ['file', 'line', 'args']);
            resolve('heyman.chain')->set('debugInfo', $info);
        }
    }
}
