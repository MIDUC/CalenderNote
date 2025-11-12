<?php

namespace App\Repositories;

use App\Models\ScheduleShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class ScheduleShareRepository extends BaseRepository
{
    /**
     * ScheduleShareRepository constructor.
     *
     * @param ScheduleShare $model
     */
    public function __construct(ScheduleShare $model)
    {
        parent::__construct($model);
    }
}