<?php

namespace App\Repositories;

use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class RecurringTransactionRepository extends BaseRepository
{
    /**
     * RecurringTransactionRepository constructor.
     *
     * @param RecurringTransaction $model
     */
    public function __construct(RecurringTransaction $model)
    {
        parent::__construct($model);
    }
}