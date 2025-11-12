<?php

namespace App\Repositories;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class TransactionRepository extends BaseRepository
{
    /**
     * TransactionRepository constructor.
     *
     * @param Transaction $model
     */
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}