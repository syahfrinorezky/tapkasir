<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'photo',
        'product_name',
        'price',
        'category_id',
        'barcode',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getStock(int $productId): int
    {
        $batchModel = new ProductBatchModel();
        $query = $batchModel->selectSum('current_stock')
            ->where('product_id', $productId)
            ->where('deleted_at', null)
            ->groupStart()
                ->where('expired_date >', date('Y-m-d'))
                ->orWhere('expired_date', null)
            ->groupEnd()
            ->first();
            
        return (int) ($query['current_stock'] ?? 0);
    }
}
