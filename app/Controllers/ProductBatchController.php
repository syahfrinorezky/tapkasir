<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductBatchModel;

class ProductBatchController extends BaseController
{
    public function productBatches($productId)
    {
        $productId = (int) $productId;
        if ($productId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Produk tidak valid']);
        }

        $batchModel = new ProductBatchModel();
        $batches = $batchModel
            ->select('product_batches.*, storage_locations.rack, storage_locations.row')
            ->join('storage_locations', 'storage_locations.id = product_batches.location_id', 'left')
            ->where('product_batches.product_id', $productId)
            ->where('product_batches.deleted_at', null)
            ->orderBy('product_batches.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['batches' => $batches]);
    }
}
