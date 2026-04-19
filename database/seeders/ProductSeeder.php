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
            ['name' => 'Ốp lưng', 'slug' => 'op-lung'],
            ['name' => 'Củ sạc', 'slug' => 'cu-sac'],
            ['name' => 'Cáp sạc', 'slug' => 'cap-sac'],
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe'],
            ['name' => 'Kính cường lực', 'slug' => 'kinh-cuong-luc'],
            ['name' => 'Sạc dự phòng', 'slug' => 'sac-du-phong'],
            ['name' => 'Giá đỡ điện thoại', 'slug' => 'gia-do-dien-thoai'],
            ['name' => 'Sạc không dây', 'slug' => 'sac-khong-day'],
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
                'name' => 'Ốp lưng chống sốc MagSafe iPhone 15 Pro',
                'slug' => 'op-lung-chong-soc-magsafe-iphone-15-pro',
                'category_slug' => 'op-lung',
                'brand_slug' => 'spigen',
                'sku' => 'ACC-CASE-001',
                'price' => 290000,
                'sale_price' => 249000,
                'warranty_months' => 6,
                'variant_name' => 'Màu đen',
                'attributes' => ['color' => 'Đen', 'compatibility' => 'iPhone 15 Pro'],
                'short_description' => 'Ốp lưng viền TPU chống sốc, hỗ trợ sạc MagSafe ổn định và cầm nắm chắc tay.',
                'image' => $this->unsplash('1511707171634-5f897ff02aa9'),
            ],
            [
                'name' => 'Củ sạc nhanh Anker Nano 20W USB-C',
                'slug' => 'cu-sac-nhanh-anker-nano-20w-usb-c',
                'category_slug' => 'cu-sac',
                'brand_slug' => 'anker',
                'sku' => 'ACC-CHARGER-001',
                'price' => 390000,
                'sale_price' => 350000,
                'warranty_months' => 12,
                'variant_name' => 'Màu trắng',
                'attributes' => ['output' => '20W', 'port' => 'USB-C'],
                'short_description' => 'Củ sạc nhanh nhỏ gọn, phù hợp iPhone và Android hỗ trợ PD.',
                'image' => $this->unsplash('1510557880182-3d4d3cba35a5'),
            ],
            [
                'name' => 'Cáp sạc UGREEN USB-C to Lightning 1m',
                'slug' => 'cap-sac-ugreen-usb-c-to-lightning-1m',
                'category_slug' => 'cap-sac',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-CABLE-001',
                'price' => 199000,
                'sale_price' => 169000,
                'warranty_months' => 12,
                'variant_name' => 'Dài 1 mét',
                'attributes' => ['length' => '1m', 'connector' => 'USB-C to Lightning'],
                'short_description' => 'Cáp bọc dù bền bỉ, sạc nhanh ổn định và chống gãy gập tốt.',
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
                'variant_name' => 'Màu trắng',
                'attributes' => ['connection' => 'Bluetooth 5.3', 'battery' => '26 giờ'],
                'short_description' => 'Tai nghe TWS chống ồn, âm thanh chi tiết và đàm thoại rõ ràng.',
                'image' => $this->unsplash('1580910051074-3eb694886505'),
            ],
            [
                'name' => 'Kính cường lực Nillkin 9H cho iPhone 15',
                'slug' => 'kinh-cuong-luc-nillkin-9h-cho-iphone-15',
                'category_slug' => 'kinh-cuong-luc',
                'brand_slug' => 'nillkin',
                'sku' => 'ACC-GLASS-001',
                'price' => 149000,
                'sale_price' => 119000,
                'warranty_months' => 6,
                'variant_name' => 'Trong suốt',
                'attributes' => ['hardness' => '9H', 'compatibility' => 'iPhone 15'],
                'short_description' => 'Kính cường lực phủ oleophobic, chống bám vân tay và dễ dán.',
                'image' => $this->unsplash('1505740420928-5e560c06d30e'),
            ],
            [
                'name' => 'Sạc dự phòng Baseus 10000mAh PD 20W',
                'slug' => 'sac-du-phong-baseus-10000mah-pd-20w',
                'category_slug' => 'sac-du-phong',
                'brand_slug' => 'baseus',
                'sku' => 'ACC-POWERBANK-001',
                'price' => 690000,
                'sale_price' => 620000,
                'warranty_months' => 12,
                'variant_name' => 'Dung lượng 10000mAh',
                'attributes' => ['capacity' => '10000mAh', 'output' => '20W'],
                'short_description' => 'Pin dự phòng hỗ trợ PD, hiển thị LED và sạc 2 thiết bị cùng lúc.',
                'image' => $this->unsplash('1546435770-a3e426bf472b'),
            ],
            [
                'name' => 'Giá đỡ điện thoại ô tô Belkin nam châm',
                'slug' => 'gia-do-dien-thoai-o-to-belkin-nam-cham',
                'category_slug' => 'gia-do-dien-thoai',
                'brand_slug' => 'belkin',
                'sku' => 'ACC-HOLDER-001',
                'price' => 249000,
                'sale_price' => 219000,
                'warranty_months' => 6,
                'variant_name' => 'Gắn taplo',
                'attributes' => ['mount' => 'Taplo', 'type' => 'Nam châm'],
                'short_description' => 'Giá đỡ xoay linh hoạt, bám chắc và gọn gàng khi di chuyển trong đô thị.',
                'image' => $this->unsplash('1572569511254-d8f925fe2cbb'),
            ],
            [
                'name' => 'Cáp sạc Baseus USB-C to USB-C 100W',
                'slug' => 'cap-sac-baseus-usb-c-to-usb-c-100w',
                'category_slug' => 'cap-sac',
                'brand_slug' => 'baseus',
                'sku' => 'ACC-CABLE-002',
                'price' => 229000,
                'sale_price' => 199000,
                'warranty_months' => 9,
                'variant_name' => 'Dài 1.2 mét',
                'attributes' => ['length' => '1.2m', 'power' => '100W'],
                'short_description' => 'Cáp sạc nhanh công suất lớn cho điện thoại, tablet và laptop mỏng nhẹ.',
                'image' => $this->unsplash('1523275335684-37898b6baf30'),
            ],
            [
                'name' => 'Tai nghe có dây UGREEN USB-C Hi-Res',
                'slug' => 'tai-nghe-co-day-ugreen-usb-c-hi-res',
                'category_slug' => 'tai-nghe',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-EARPHONE-002',
                'price' => 450000,
                'sale_price' => 390000,
                'warranty_months' => 9,
                'variant_name' => 'Đầu USB-C',
                'attributes' => ['connector' => 'USB-C', 'audio' => 'Hi-Res'],
                'short_description' => 'Tai nghe có mic, phù hợp nghe nhạc và gọi điện hằng ngày.',
                'image' => $this->unsplash('1516724562728-afc824a36e84'),
            ],
            [
                'name' => 'Đế sạc không dây Belkin BoostCharge 15W',
                'slug' => 'de-sac-khong-day-belkin-boostcharge-15w',
                'category_slug' => 'sac-khong-day',
                'brand_slug' => 'belkin',
                'sku' => 'ACC-WIRELESS-001',
                'price' => 590000,
                'sale_price' => 520000,
                'warranty_months' => 10,
                'variant_name' => 'Công suất 15W',
                'attributes' => ['output' => '15W', 'type' => 'Wireless Charger'],
                'short_description' => 'Đế sạc không dây gọn gàng cho bàn làm việc, sạc xuyên ốp mỏng.',
                'image' => $this->unsplash('1598327105666-5b89351aff97'),
            ],
            [
                'name' => 'Ốp lưng trong suốt chống ố vàng iPhone 14',
                'slug' => 'op-lung-trong-suot-chong-o-vang-iphone-14',
                'category_slug' => 'op-lung',
                'brand_slug' => 'spigen',
                'sku' => 'ACC-CASE-002',
                'price' => 260000,
                'sale_price' => 229000,
                'warranty_months' => 6,
                'variant_name' => 'Trong suốt',
                'attributes' => ['color' => 'Trong suốt', 'compatibility' => 'iPhone 14'],
                'short_description' => 'Ốp lưng chống ố vàng, viền chống sốc và giữ vẻ đẹp nguyên bản của máy.',
                'image' => $this->unsplash('1521572163474-6864f9cf17ab'),
            ],
            [
                'name' => 'Củ sạc UGREEN GaN 30W 2 cổng',
                'slug' => 'cu-sac-ugreen-gan-30w-2-cong',
                'category_slug' => 'cu-sac',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-CHARGER-002',
                'price' => 520000,
                'sale_price' => 469000,
                'warranty_months' => 12,
                'variant_name' => 'GaN 30W',
                'attributes' => ['output' => '30W', 'port' => 'USB-C + USB-A'],
                'short_description' => 'Củ sạc GaN công suất cao, sạc nhanh ổn định cho điện thoại và tablet.',
                'image' => $this->unsplash('1518770660439-4636190af475'),
            ],
            [
                'name' => 'Cáp sạc Anker USB-C to USB-C 60W 1.8m',
                'slug' => 'cap-sac-anker-usb-c-to-usb-c-60w-1-8m',
                'category_slug' => 'cap-sac',
                'brand_slug' => 'anker',
                'sku' => 'ACC-CABLE-003',
                'price' => 279000,
                'sale_price' => 239000,
                'warranty_months' => 12,
                'variant_name' => 'Dài 1.8 mét',
                'attributes' => ['length' => '1.8m', 'power' => '60W'],
                'short_description' => 'Cáp nylon bền chắc, hỗ trợ sạc nhanh 60W và truyền dữ liệu ổn định.',
                'image' => $this->unsplash('1517336714739-489689fd1ca8'),
            ],
            [
                'name' => 'Tai nghe Bluetooth Baseus Bowie E18',
                'slug' => 'tai-nghe-bluetooth-baseus-bowie-e18',
                'category_slug' => 'tai-nghe',
                'brand_slug' => 'baseus',
                'sku' => 'ACC-EARBUD-002',
                'price' => 990000,
                'sale_price' => 890000,
                'warranty_months' => 12,
                'variant_name' => 'Màu đen',
                'attributes' => ['connection' => 'Bluetooth 5.3', 'battery' => '30 giờ'],
                'short_description' => 'Tai nghe không dây độ trễ thấp, âm bass tốt và đeo êm khi dùng lâu.',
                'image' => $this->unsplash('1546435770-a3e426bf472b'),
            ],
            [
                'name' => 'Kính cường lực full màn hình iPhone 14 Pro',
                'slug' => 'kinh-cuong-luc-full-man-hinh-iphone-14-pro',
                'category_slug' => 'kinh-cuong-luc',
                'brand_slug' => 'nillkin',
                'sku' => 'ACC-GLASS-002',
                'price' => 179000,
                'sale_price' => 149000,
                'warranty_months' => 6,
                'variant_name' => 'Viền đen',
                'attributes' => ['hardness' => '9H', 'compatibility' => 'iPhone 14 Pro'],
                'short_description' => 'Kính cường lực full keo, hiển thị trong trẻo và thao tác cảm ứng mượt mà.',
                'image' => $this->unsplash('1512496015851-a90fb38ba796'),
            ],
            [
                'name' => 'Sạc dự phòng Anker 20000mAh PD 30W',
                'slug' => 'sac-du-phong-anker-20000mah-pd-30w',
                'category_slug' => 'sac-du-phong',
                'brand_slug' => 'anker',
                'sku' => 'ACC-POWERBANK-002',
                'price' => 1290000,
                'sale_price' => 1150000,
                'warranty_months' => 18,
                'variant_name' => 'Dung lượng 20000mAh',
                'attributes' => ['capacity' => '20000mAh', 'output' => '30W'],
                'short_description' => 'Dung lượng lớn cho nhiều lần sạc, hỗ trợ PD 30W cho thiết bị đời mới.',
                'image' => $this->unsplash('1587033411391-5d9e51cce126'),
            ],
            [
                'name' => 'Giá đỡ điện thoại để bàn UGREEN gập gọn',
                'slug' => 'gia-do-dien-thoai-de-ban-ugreen-gap-gon',
                'category_slug' => 'gia-do-dien-thoai',
                'brand_slug' => 'ugreen',
                'sku' => 'ACC-HOLDER-002',
                'price' => 159000,
                'sale_price' => 129000,
                'warranty_months' => 6,
                'variant_name' => 'Màu bạc',
                'attributes' => ['mount' => 'Để bàn', 'type' => 'Gập gọn'],
                'short_description' => 'Giá đỡ gập gọn tiện mang theo, chỉnh góc linh hoạt khi học và làm việc.',
                'image' => $this->unsplash('1527864550417-7fd91fc51a46'),
            ],
            [
                'name' => 'Đế sạc không dây Anker MagGo 15W',
                'slug' => 'de-sac-khong-day-anker-maggo-15w',
                'category_slug' => 'sac-khong-day',
                'brand_slug' => 'anker',
                'sku' => 'ACC-WIRELESS-002',
                'price' => 890000,
                'sale_price' => 799000,
                'warranty_months' => 12,
                'variant_name' => 'Nam châm 15W',
                'attributes' => ['output' => '15W', 'type' => 'MagSafe Compatible'],
                'short_description' => 'Đế sạc nam châm bám chắc, tối ưu cho iPhone hỗ trợ MagSafe.',
                'image' => $this->unsplash('1583394838336-acd977736f90'),
            ],
            [
                'name' => 'Tai nghe chụp tai SoundPEATS Space',
                'slug' => 'tai-nghe-chup-tai-soundpeats-space',
                'category_slug' => 'tai-nghe',
                'brand_slug' => 'soundpeats',
                'sku' => 'ACC-HEADPHONE-001',
                'price' => 1590000,
                'sale_price' => 1390000,
                'warranty_months' => 12,
                'variant_name' => 'Màu xanh navy',
                'attributes' => ['connection' => 'Bluetooth 5.2', 'battery' => '100 giờ'],
                'short_description' => 'Tai nghe chụp tai chống ồn chủ động, pin lâu và đeo thoải mái cả ngày.',
                'image' => $this->unsplash('1505740420928-5e560c06d30e'),
            ],
            [
                'name' => 'Ốp lưng chống sốc có khe thẻ Samsung S24',
                'slug' => 'op-lung-chong-soc-co-khe-the-samsung-s24',
                'category_slug' => 'op-lung',
                'brand_slug' => 'nillkin',
                'sku' => 'ACC-CASE-003',
                'price' => 320000,
                'sale_price' => 279000,
                'warranty_months' => 6,
                'variant_name' => 'Màu xanh đậm',
                'attributes' => ['color' => 'Xanh đậm', 'compatibility' => 'Samsung S24'],
                'short_description' => 'Ốp lưng cứng cáp có khe thẻ tiện lợi, bảo vệ tốt khi va đập hằng ngày.',
                'image' => $this->unsplash('1511499767150-a48a237f0083'),
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