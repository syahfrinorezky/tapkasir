<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\StorageLocationModel;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends BaseController
{
    public function index()
    {
        if ($this->request->isAJAX()) {
            return $this->data();
        }
        return view('app/admin/master-data/product');
    }

    public function data()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $locationModel = new StorageLocationModel();

        $products = $productModel
            ->select('products.*, categories.category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.deleted_at', null)
            ->findAll();

        $categories = $categoryModel
            ->select('*')
            ->where('deleted_at', null)
            ->findAll();

        $locations = $locationModel
            ->where('deleted_at', null)
            ->orderBy('rack', 'ASC')
            ->orderBy('row', 'ASC')
            ->findAll();

        foreach ($products as &$product) {
            if (!empty($product['photo'])) {
                $path = ROOTPATH . 'public/uploads/products/' . basename($product['photo']);
                if (file_exists($path)) {
                    $product['photo'] = base_url('uploads/products/' . basename($product['photo']));
                } else {
                    $product['photo'] = null;
                }
            }
            $product['stock'] = $productModel->getStock($product['id']);
        }

        return $this->response->setJSON([
            'products' => $products,
            'categories' => $categories,
            'locations' => $locations
        ]);
    }

    private function generateBarcode($categoryId)
    {
        $productModel = new ProductModel();
        $appCode = '880';
        $categoryCode = str_pad($categoryId, 3, '0', STR_PAD_LEFT);

        $lastProduct = $productModel
            ->where('category_id', $categoryId)
            ->withDeleted()
            ->orderBy('id', 'DESC')
            ->first();

        $sequence = 1;
        if ($lastProduct && !empty($lastProduct['barcode'])) {
            $lastSequence = (int) substr($lastProduct['barcode'], 6, 5);
            $sequence = $lastSequence + 1;
        }

        $sequenceCode = str_pad($sequence, 5, '0', STR_PAD_LEFT);
        return $appCode . $categoryCode . $sequenceCode;
    }


    public function add()
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'category_id' => $this->request->getPost('category_id'),
        ];

        $rules = (new \App\Validation\ProductRules())->create;
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $this->validator->getErrors(),
            ]);
        }

        if (empty($data['category_id'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Kategori harus dipilih'
            ]);
        }

        $category = $categoryModel->find($data['category_id']);
        if (!$category) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Kategori tidak ditemukan'
            ]);
        }
        $data['barcode'] = $this->generateBarcode($data['category_id']);

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $mime = $photo->getMimeType();
            if (!in_array($mime, ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Format gambar tidak valid (hanya jpg, jpeg, png, webp)'
                ]);
            }

            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/products', $newName);
            $data['photo'] = 'uploads/products/' . $newName;
        }

        if ($productModel->insert($data)) {
            $photoUrl = null;
            if (!empty($data['photo'])) {
                $photoUrl = base_url($data['photo']);
            }
            return $this->response->setJSON([
                'message' => 'Produk berhasil ditambahkan',
                'barcode' => $data['barcode'],
                'photo' => $photoUrl
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal menambahkan produk: ' . implode(', ', $productModel->errors())
        ]);
    }

    public function edit($id)
    {
        $productModel = new ProductModel();
        $data = [
            'product_name' => $this->request->getPost('product_name'),
            'price' => $this->request->getPost('price'),
            'category_id' => $this->request->getPost('category_id'),
        ];

        $rules = (new \App\Validation\ProductRules())->update;
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $this->validator->getErrors(),
            ]);
        }

        $currentProduct = $productModel->find($id);
        if ($currentProduct && $currentProduct['category_id'] != $data['category_id']) {
            $data['barcode'] = $this->generateBarcode($data['category_id']);
        }

        $photo = $this->request->getFile('photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $mime = $photo->getMimeType();
            if (!in_array($mime, ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'])) {
                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Format gambar tidak valid (hanya jpg, jpeg, png, webp)'
                ]);
            }

            $newName = $photo->getRandomName();
            $photo->move(FCPATH . 'uploads/products', $newName);
            $data['photo'] = 'uploads/products/' . $newName;
            if ($currentProduct && $currentProduct['photo']) {
                $oldPhotoPath = FCPATH . $currentProduct['photo'];
                if (file_exists($oldPhotoPath))
                    unlink($oldPhotoPath);
            }
        }

        if ($productModel->update($id, $data)) {
            $updated = $productModel->find($id);
            $photoUrl = null;
            if ($updated && !empty($updated['photo'])) {
                $photoUrl = base_url($updated['photo']);
            }
            return $this->response->setJSON([
                'message' => 'Produk berhasil diperbarui',
                'photo' => $photoUrl
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal memperbarui produk: ' . implode(', ', $productModel->errors())
        ]);
    }

    public function delete($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);

        if (!$product) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Produk tidak ditemukan'
            ]);
        }

        if ($productModel->delete($id)) {
            return $this->response->setJSON([
                'message' => 'Produk berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal menghapus produk'
        ]);
    }

    public function addCategory()
    {
        $categoryModel = new CategoryModel();
        $data = ['category_name' => $this->request->getPost('category_name')];

        $rules = (new \App\Validation\CategoryRules())->create;
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $this->validator->getErrors(),
            ]);
        }

        if ($categoryModel->insert($data)) {
            return $this->response->setJSON([
                'message' => 'Kategori berhasil ditambahkan'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal menambahkan kategori: ' . implode(', ', $categoryModel->errors())
        ]);
    }

    public function editCategory($id)
    {
        $categoryModel = new CategoryModel();
        $data = ['category_name' => $this->request->getPost('category_name')];

        $rules = (new \App\Validation\CategoryRules())->update;
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal.',
                'validation' => $this->validator->getErrors(),
            ]);
        }

        if ($categoryModel->update($id, $data)) {
            return $this->response->setJSON([
                'message' => 'Kategori berhasil diperbarui'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal memperbarui kategori: ' . implode(', ', $categoryModel->errors())
        ]);
    }

    public function deleteCategory($id)
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        $productsInCategory = $productModel->where('category_id', $id)->where('deleted_at IS NULL')->countAllResults();

        if ($productsInCategory > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Tidak dapat menghapus kategori karena masih digunakan oleh ' . $productsInCategory . ' produk'
            ]);
        }

        if ($categoryModel->delete($id)) {
            return $this->response->setJSON([
                'message' => 'Kategori berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(500)->setJSON([
            'message' => 'Gagal menghapus kategori'
        ]);
    }

    public function barcodeImage($barcode)
    {
        try {
            if (!class_exists(BarcodeGeneratorPNG::class)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Barcode generator not installed.'
                ]);
            }

            if (!extension_loaded('gd') && !extension_loaded('imagick')) {
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                $svg = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 60);
                return $this->response->setHeader('Content-Type', 'image/svg+xml')->setBody($svg);
            }

            $generator = new BarcodeGeneratorPNG();
            $png = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 60);

            return $this->response->setHeader('Content-Type', 'image/png')->setBody($png);
        } catch (\Throwable $th) {
            log_message('error', '[ProductController::barcodeImage] ' . $th->getMessage());
            return $this->response->setStatusCode(500)->setBody('Gagal memuat barcode');
        }
    }

    public function barcodeSave($barcode)
    {
        try {
            if (!class_exists(BarcodeGeneratorPNG::class)) {
                return $this->response->setStatusCode(500)->setJSON([
                    'message' => 'Barcode generator not installed. Run: composer require picqer/php-barcode-generator'
                ]);
            }

            $dir = ROOTPATH . 'public/uploads/barcodes/';
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal membuat direktori barcodes']);
                }
            }

            $safeName = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $barcode);

            if (!extension_loaded('gd') && !extension_loaded('imagick')) {
                $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
                $content = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 60);
                $ext = '.svg';
            } else {
                $generator = new BarcodeGeneratorPNG();
                $content = $generator->getBarcode($barcode, $generator::TYPE_CODE_128, 2, 60);
                $ext = '.png';
            }

            $filename = $safeName . $ext;
            $filePath = $dir . $filename;

            if (file_put_contents($filePath, $content) === false) {
                return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menyimpan gambar barcode']);
            }

            $url = base_url('uploads/barcodes/' . $filename);

            return $this->response->setJSON(['message' => 'ok', 'url' => $url]);
        } catch (\Throwable $th) {
            log_message('error', '[ProductController::barcodeSave] ' . $th->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'message' => 'Terjadi kesalahan saat menyimpan barcode'
            ]);
        }
    }

    public function addLocation()
    {
        $locationModel = new StorageLocationModel();

        $rules = [
            'rack' => 'required',
            'row' => 'required',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal',
                'validation' => $this->validator->getErrors()
            ]);
        }

        $rack = $this->request->getPost('rack');
        $row = $this->request->getPost('row');

        $exists = $locationModel->where('rack', $rack)->where('row', $row)->where('deleted_at', null)->first();
        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Lokasi Rack dan Row ini sudah ada.']);
        }

        $data = [
            'rack' => $rack,
            'row' => $row,
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status')
        ];

        if ($locationModel->insert($data)) {
            return $this->response->setJSON(['message' => 'Lokasi berhasil ditambahkan']);
        }
        return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menambah lokasi']);
    }

    public function editLocation($id)
    {
        $locationModel = new StorageLocationModel();

        $rules = [
            'rack' => 'required',
            'row' => 'required',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Validasi gagal',
                'validation' => $this->validator->getErrors()
            ]);
        }

        $rack = $this->request->getPost('rack');
        $row = $this->request->getPost('row');

        $exists = $locationModel->where('rack', $rack)->where('row', $row)->where('id !=', $id)->where('deleted_at', null)->first();
        if ($exists) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Lokasi Rack dan Row ini sudah ada.']);
        }

        $data = [
            'rack' => $rack,
            'row' => $row,
            'description' => $this->request->getPost('description'),
            'status' => $this->request->getPost('status')
        ];

        if ($locationModel->update($id, $data)) {
            return $this->response->setJSON(['message' => 'Lokasi berhasil diperbarui']);
        }
        return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal memperbarui lokasi']);
    }

    public function deleteLocation($id)
    {
        $locationModel = new StorageLocationModel();

        // Cek apakah lokasi masih digunakan oleh produk (batch)
        $batchModel = new \App\Models\ProductBatchModel();
        $usageCount = $batchModel->where('location_id', $id)->where('current_stock >', 0)->where('deleted_at', null)->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Tidak dapat menghapus lokasi karena sedang digunakan oleh ' . $usageCount . ' batch produk aktif.'
            ]);
        }

        // Juga cek apakah ada batch (meskipun stok 0 tapi belum dihapus historynya) jika ingin strict, 
        // tapi user bilang "masih dipakai oleh produk", biasanya itu stok > 0. 

        if ($locationModel->delete($id)) {
            return $this->response->setJSON(['message' => 'Lokasi berhasil dihapus']);
        }
        return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal menghapus lokasi']);
    }

    public function trashData()
    {
        $productModel = new ProductModel();

        $products = $productModel
            ->select('products.*, categories.category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->onlyDeleted()
            ->findAll();

        foreach ($products as &$product) {
            if (!empty($product['photo'])) {
                $path = ROOTPATH . 'public/uploads/products/' . basename($product['photo']);
                if (file_exists($path)) {
                    $product['photo'] = base_url('uploads/products/' . basename($product['photo']));
                } else {
                    $product['photo'] = null;
                }
            }
            $product['stock'] = $productModel->getStock($product['id']);
        }

        return $this->response->setJSON([
            'products' => $products
        ]);
    }

    public function restore($id = null)
    {
        $productModel = new ProductModel();
        $categoryModel = new CategoryModel();

        $ids = $id ? [$id] : ($this->request->getJSON()->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }


        $products = $productModel->onlyDeleted()->whereIn('id', $ids)->findAll();
        foreach ($products as $p) {
            $category = $categoryModel->onlyDeleted()->find($p['category_id']);
            if ($category) {

                return $this->response->setStatusCode(400)->setJSON([
                    'message' => 'Kategori untuk produk "' . $p['product_name'] . '" telah dihapus. Silakan pulihkan kategori terlebih dahulu.'
                ]);
            }
        }

        $productModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
        return $this->response->setJSON(['message' => ($id ? 'Produk' : count($ids) . ' produk') . ' berhasil dipulihkan']);
    }

    public function deletePermanent($id = null)
    {
        $productModel = new ProductModel();
        $transactionItemModel = new \App\Models\TransactionItemModel();

        $ids = $id ? [$id] : ($this->request->getJSON()->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }


        $usageCount = $transactionItemModel->whereIn('product_id', $ids)->withDeleted()->countAllResults();
        if ($usageCount > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Tidak dapat menghapus permanen ' . ($id ? 'data' : count($ids) . ' data') . ' ini karena memiliki riwayat penjualan.'
            ]);
        }

        $products = $productModel->onlyDeleted()->whereIn('id', $ids)->findAll();
        foreach ($products as $product) {
            if (!empty($product['photo'])) {
                $photoPath = ROOTPATH . 'public/' . $product['photo'];
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
        }

        $productModel->builder()->whereIn('id', $ids)->delete();
        return $this->response->setJSON(['message' => ($id ? 'Produk' : count($ids) . ' produk') . ' berhasil dihapus permanen']);
    }

    // --- Category Trash Methods ---

    public function trashCategoriesData()
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->onlyDeleted()->findAll();
        return $this->response->setJSON(['categories' => $categories]);
    }

    public function restoreCategory($id = null)
    {
        $categoryModel = new CategoryModel();

        if ($id) {
            $categoryModel->builder()->where('id', $id)->update(['deleted_at' => null]);
            return $this->response->setJSON(['message' => 'Kategori berhasil dipulihkan']);
        }

        $ids = $this->request->getJSON()->ids ?? [];
        if (!empty($ids)) {
            $categoryModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
            return $this->response->setJSON(['message' => count($ids) . ' kategori berhasil dipulihkan']);
        }

        return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
    }

    public function deletePermanentCategory($id = null)
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        $ids = $id ? [$id] : ($this->request->getJSON()->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $usageCount = $productModel->withDeleted()->whereIn('category_id', $ids)->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Tidak dapat menghapus permanen ' . ($id ? 'kategori' : count($ids) . ' kategori') . ' ini karena masih digunakan oleh ' . $usageCount . ' produk (termasuk di sampah).'
            ]);
        }

        $categoryModel->builder()->whereIn('id', $ids)->delete();
        return $this->response->setJSON(['message' => ($id ? 'Kategori' : count($ids) . ' kategori') . ' berhasil dihapus permanen']);
    }

    // --- Location Trash Methods ---

    public function trashLocationsData()
    {
        $locationModel = new StorageLocationModel();
        $locations = $locationModel->onlyDeleted()->findAll();
        return $this->response->setJSON(['locations' => $locations]);
    }

    public function restoreLocation($id = null)
    {
        $locationModel = new StorageLocationModel();

        if ($id) {
            $locationModel->builder()->where('id', $id)->update(['deleted_at' => null]);
            return $this->response->setJSON(['message' => 'Lokasi berhasil dipulihkan']);
        }

        $ids = $this->request->getJSON()->ids ?? [];
        if (!empty($ids)) {
            $locationModel->builder()->whereIn('id', $ids)->update(['deleted_at' => null]);
            return $this->response->setJSON(['message' => count($ids) . ' lokasi berhasil dipulihkan']);
        }

        return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
    }

    public function deletePermanentLocation($id = null)
    {
        $locationModel = new StorageLocationModel();
        $batchModel = new \App\Models\ProductBatchModel();

        $ids = $id ? [$id] : ($this->request->getJSON()->ids ?? []);

        if (empty($ids)) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tidak ada data yang dipilih']);
        }

        $usageCount = $batchModel->withDeleted()->whereIn('location_id', $ids)->countAllResults();

        if ($usageCount > 0) {
            return $this->response->setStatusCode(400)->setJSON([
                'message' => 'Tidak dapat menghapus permanen ' . ($id ? 'lokasi' : count($ids) . ' lokasi') . ' ini karena masih digunakan oleh ' . $usageCount . ' batch produk.'
            ]);
        }

        $locationModel->builder()->whereIn('id', $ids)->delete();
        return $this->response->setJSON(['message' => ($id ? 'Lokasi' : count($ids) . ' lokasi') . ' berhasil dihapus permanen']);
    }
}
