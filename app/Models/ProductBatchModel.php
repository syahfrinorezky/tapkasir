<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductBatchModel extends Model
{
    protected $table            = 'product_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'batch_code',
        'expired_date',
        'purchase_price',
        'initial_stock',
        'current_stock',
        'location_id',
        'receipt_image',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
