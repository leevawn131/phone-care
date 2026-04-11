<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Op lung', 'slug' => 'op-lung'],
            ['name' => 'Cu sac', 'slug' => 'cu-sac'],
            ['name' => 'Cap sac', 'slug' => 'cap-sac'],
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe'],
            ['name' => 'Kinh cuong luc', 'slug' => 'kinh-cuong-luc'],
            ['name' => 'Sac du phong', 'slug' => 'sac-du-phong'],
            ['name' => 'Gia do dien thoai', 'slug' => 'gia-do-dien-thoai'],
            ['name' => 'Sac khong day', 'slug' => 'sac-khong-day'],
        ])->mapWithKeys(function (array $category): array {
            $model = Category::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'parent_id' => null,
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            return [$category['slug'] => $model];
        });

        $brands = collect([
            ['name' => 'Spigen', 'slug' => 'spigen'],
            ['name' => 'Anker', 'slug' => 'anker'],
            ['name' => 'UGREEN', 'slug' => 'ugreen'],
            ['name' => 'Baseus', 'slug' => 'baseus'],
            ['name' => 'Nillkin', 'slug' => 'nillkin'],
            ['name' => 'Belkin', 'slug' => 'belkin'],
            ['name' => 'SoundPEATS', 'slug' => 'soundpeats'],
        ])->mapWithKeys(function (array $brand): array {
            $model = Brand::query()->updateOrCreate(
                ['slug' => $brand['slug']],
                [
                    'name' => $brand['name'],
                    'is_active' => true,
                ]
            );

            return [$brand['slug'] => $model];
        });

        $products = [
            [
                'name' => 'Op lung chong soc MagSafe iPhone 15 Pro',
                'slug' => 'op-lung-chong-soc-magsafe-iphone-15-pro',
                'category_slug' => 'op-lung',
                'brand_slug' => 'spigen',
                'sku' => 'ACC-CASE-001',
                'price' => 290000,
                'sale_price' => 249000,
                'warranty_months' => 6,
                'variant_name' => 'Mau den',
                'attributes' => ['color' => 'Den', 'compatibility' => 'iPhone 15 Pro'],
                'short_description' => 'Op lung vien TPU chong soc, ho tro sac MagSafe on dinh va cam nam chac tay.',
                'image' => $this->unsplash('1511707171634-5f897ff02aa9'),
            ],
            [
                'name' => 'Cu sac nhanh Anker Nano 20W USB-C',
                'slug' => 'cu-sac-nhanh-anker-nano-20w-usb-c',
                'category_slug' => 'cu-sac',
                'brand_slug' => 'anker',
                'sku' => 'ACC-CHARGER-001',
                'price' => 390000,
                'sale_price' => 350000,
                'warranty_months' => 12,
                'variant_name' => 'Mau trang',
                'attributes' => ['output' => '20W', 'port' => 'USB-C'],
                'short_description' => 'Cu sac nhanh nho gon, phu hop iPhone va Android ho tro PD.',
                'image' => $this->unsplash('1510557880182-3d4d3cba35a5'),
            ],
            [
                'name' => 'Cap sac UGREEN USB-C to Lightning 1m',
                'slug' => 'cap-sac-ugreen-usb-c-to-lightning-1m',
                'category_slug' => 'cap-sac',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-CABLE-001',
                'price' => 199000,
                'sale_price' => 169000,
                'warranty_months' => 12,
                'variant_name' => 'Dai 1 met',
                'attributes' => ['length' => '1m', 'connector' => 'USB-C to Lightning'],
                'short_description' => 'Cap boc du ben bi, sac nhanh on dinh va chong gay gap tot.',
                'image' => $this->unsplash('1546868871-7041f2a55e12'),
            ],
            [
                'name' => 'Tai nghe Bluetooth SoundPEATS Air 4',
                'slug' => 'tai-nghe-bluetooth-soundpeats-air-4',
                'category_slug' => 'tai-nghe',
                'brand_slug' => 'soundpeats',
                'sku' => 'ACC-EARBUD-001',
                'price' => 1290000,
                'sale_price' => 1150000,
                'warranty_months' => 12,
                'variant_name' => 'Mau trang',
                'attributes' => ['connection' => 'Bluetooth 5.3', 'battery' => '26 gio'],
                'short_description' => 'Tai nghe TWS chong on, am thanh chi tiet va dam thoai ro rang.',
                'image' => $this->unsplash('1580910051074-3eb694886505'),
            ],
            [
                'name' => 'Kinh cuong luc Nillkin 9H cho iPhone 15',
                'slug' => 'kinh-cuong-luc-nillkin-9h-cho-iphone-15',
                'category_slug' => 'kinh-cuong-luc',
                'brand_slug' => 'nillkin',
                'sku' => 'ACC-GLASS-001',
                'price' => 149000,
                'sale_price' => 119000,
                'warranty_months' => 6,
                'variant_name' => 'Trong suot',
                'attributes' => ['hardness' => '9H', 'compatibility' => 'iPhone 15'],
                'short_description' => 'Kinh cuong luc phu oleophobic, chong bam van tay va de dan.',
                'image' => $this->unsplash('1505740420928-5e560c06d30e'),
            ],
            [
                'name' => 'Sac du phong Baseus 10000mAh PD 20W',
                'slug' => 'sac-du-phong-baseus-10000mah-pd-20w',
                'category_slug' => 'sac-du-phong',
                'brand_slug' => 'baseus',
                'sku' => 'ACC-POWERBANK-001',
                'price' => 690000,
                'sale_price' => 620000,
                'warranty_months' => 12,
                'variant_name' => 'Dung luong 10000mAh',
                'attributes' => ['capacity' => '10000mAh', 'output' => '20W'],
                'short_description' => 'Pin du phong ho tro PD, hien thi LED va sac 2 thiet bi cung luc.',
                'image' => $this->unsplash('1546435770-a3e426bf472b'),
            ],
            [
                'name' => 'Gia do dien thoai o to Belkin nam cham',
                'slug' => 'gia-do-dien-thoai-o-to-belkin-nam-cham',
                'category_slug' => 'gia-do-dien-thoai',
                'brand_slug' => 'belkin',
                'sku' => 'ACC-HOLDER-001',
                'price' => 249000,
                'sale_price' => 219000,
                'warranty_months' => 6,
                'variant_name' => 'Gan taplo',
                'attributes' => ['mount' => 'Taplo', 'type' => 'Nam cham'],
                'short_description' => 'Gia do xoay linh hoat, bam chac va gon gang khi di chuyen trong do thi.',
                'image' => $this->unsplash('1572569511254-d8f925fe2cbb'),
            ],
            [
                'name' => 'Cap sac Baseus USB-C to USB-C 100W',
                'slug' => 'cap-sac-baseus-usb-c-to-usb-c-100w',
                'category_slug' => 'cap-sac',
                'brand_slug' => 'baseus',
                'sku' => 'ACC-CABLE-002',
                'price' => 229000,
                'sale_price' => 199000,
                'warranty_months' => 9,
                'variant_name' => 'Dai 1.2 met',
                'attributes' => ['length' => '1.2m', 'power' => '100W'],
                'short_description' => 'Cap sac nhanh cong suat lon cho dien thoai, tablet va laptop mong nhe.',
                'image' => $this->unsplash('1523275335684-37898b6baf30'),
            ],
            [
                'name' => 'Tai nghe co day UGREEN USB-C Hi-Res',
                'slug' => 'tai-nghe-co-day-ugreen-usb-c-hi-res',
                'category_slug' => 'tai-nghe',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-EARPHONE-002',
                'price' => 450000,
                'sale_price' => 390000,
                'warranty_months' => 9,
                'variant_name' => 'Dau USB-C',
                'attributes' => ['connector' => 'USB-C', 'audio' => 'Hi-Res'],
                'short_description' => 'Tai nghe co mic, phu hop nghe nhac va goi dien hang ngay.',
                'image' => $this->unsplash('1516724562728-afc824a36e84'),
            ],
            [
                'name' => 'De sac khong day Belkin BoostCharge 15W',
                'slug' => 'de-sac-khong-day-belkin-boostcharge-15w',
                'category_slug' => 'sac-khong-day',
                'brand_slug' => 'belkin',
                'sku' => 'ACC-WIRELESS-001',
                'price' => 590000,
                'sale_price' => 520000,
                'warranty_months' => 10,
                'variant_name' => 'Cong suat 15W',
                'attributes' => ['output' => '15W', 'type' => 'Wireless Charger'],
                'short_description' => 'De sac khong day gon gang cho ban lam viec, sac xuyen op mong.',
                'image' => $this->unsplash('1598327105666-5b89351aff97'),
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::query()->updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $categories[$productData['category_slug']]->id,
                    'brand_id' => $brands[$productData['brand_slug']]->id,
                    'name' => $productData['name'],
                    'short_description' => $productData['short_description'],
                    'description' => $productData['short_description'],
                    'base_warranty_months' => $productData['warranty_months'],
                    'has_serial_tracking' => true,
                    'is_active' => true,
                ]
            );

            $variant = ProductVariant::query()->updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'product_id' => $product->id,
                    'barcode' => null,
                    'variant_name' => $productData['variant_name'],
                    'attributes' => $productData['attributes'],
                    'price' => $productData['price'],
                    'sale_price' => $productData['sale_price'],
                    'cost_price' => (int) round($productData['price'] * 0.7),
                    'stock' => 25,
                    'is_active' => true,
                ]
            );

            ProductImage::query()->updateOrCreate(
                [
                    'product_id' => $product->id,
                    'path' => $productData['image'],
                ],
                [
                    'product_variant_id' => $variant->id,
                    'alt_text' => $productData['name'],
                    'is_primary' => true,
                    'sort_order' => 1,
                ]
            );
        }
    }

    protected function unsplash(string $photoId): string
    {
        return sprintf(
            'https://images.unsplash.com/photo-%s?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
            $photoId
        );
    }
}