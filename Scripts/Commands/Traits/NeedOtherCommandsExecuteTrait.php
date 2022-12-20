<?php

namespace Scripts\Commands\Traits;

trait NeedOtherCommandsExecuteTrait
{
	/** 
	 * Execute with other commands supplied
	 */
	abstract static public function execute(array $otherCommands);
}
