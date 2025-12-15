<?php

namespace App\Libraries;

use App\Models\ProductBatchModel;
use App\Models\ProductModel;

class StockService
{


    public static function getAvailableBatches(int $productId): array
    {
        $batchModel = new ProductBatchModel();
        return $batchModel
            ->where('product_id', $productId)
            ->where('deleted_at', null)
            ->groupStart()
            ->where('expired_date >', date('Y-m-d'))
            ->orWhere('expired_date', null)
            ->groupEnd()
            ->where('current_stock >', 0)
            ->orderBy('(expired_date IS NULL)', 'ASC') 
            ->orderBy('expired_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public static function allocateFromBatches(int $productId, int $qty): array
    {
        $qty = max(0, $qty);
        if ($qty === 0) return [];

        $batches = self::getAvailableBatches($productId);
        $alloc = [];
        $remaining = $qty;

        foreach ($batches as $b) {
            if ($remaining <= 0) break;
            $take = min($remaining, (int) $b['current_stock']);
            if ($take <= 0) continue;
            $alloc[] = [
                'batch_id' => (int) $b['id'],
                'qty' => $take,
                'purchase_price' => (float) $b['purchase_price'],
            ];
            $remaining -= $take;
        }

        if ($remaining > 0) {
            throw new \RuntimeException('Stok batch tidak mencukupi');
        }

        return $alloc;
    }
}
