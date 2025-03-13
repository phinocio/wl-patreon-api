<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('patreon:update-data')->everyFiveMinutes();
Schedule::command('patreon:refresh-token')->everyMinute();
