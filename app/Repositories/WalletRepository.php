<?php

namespace App\Repositories;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class WalletRepository extends BaseRepository
{
    /**
     * WalletRepository constructor.
     *
     * @param Wallet $model
     */
    public function __construct(Wallet $model)
    {
        parent::__construct($model);
    }
}