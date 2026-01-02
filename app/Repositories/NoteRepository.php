<?php

namespace App\Repositories;

use App\Models\Note;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Log;

class NoteRepository extends BaseRepository
{
    /**
     * NoteRepository constructor.
     *
     * @param Note $model
     */
    public function __construct(Note $model)
    {
        parent::__construct($model);
    }
}