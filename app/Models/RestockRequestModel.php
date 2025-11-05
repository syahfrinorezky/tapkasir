<?php

namespace App\Models;

use CodeIgniter\Model;

class RestockRequestModel extends Model
{
    protected $table = 'restock_requests';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'product_id',
        'user_id',
        'quantity',
        'note',
        'status',
        'approved_by',
        'decision_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
