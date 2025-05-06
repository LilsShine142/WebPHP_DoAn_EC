<?php

class ProductVariationInstanceGateway
{
    private PDO $conn;

    public function __construct(Database $db)
    {
        $this->conn = $db->getConnection();
    }

    public function create(array $data): array | false
    {
        $imageFileName = null;

        // Xử lý ảnh Base64 nếu có
        if (!empty($data["image_base64"])) {
            $imageFileName = $this->saveBase64Image($data["image_base64"]);
            if ($imageFileName === false) {
                return false;
            }
            $data["image_name"] = $imageFileName;
        } else {
            $data["image_name"] = $data["image_name"] ?? "default.webp";
        }

        $sql = "INSERT INTO product_variations (
            product_id,
            watch_size_mm,
            watch_color,
            price_cents,
            base_price_cents,
            image_name,
            display_size_mm,
            display_type,
            resolution_h_px,
            resolution_w_px,
            ram_bytes,
            rom_bytes,
            os_id,
            connectivity,
            battery_life_mah,
            water_resistance_value,
            water_resistance_unit,
            sensor,
            case_material,
            band_material,
            band_size_mm,
            band_color,
            weight_milligrams,
            release_date,
            stop_selling
        ) VALUES (
            :product_id,
            :watch_size_mm,
            :watch_color,
            :price_cents,
            :base_price_cents,
            :image_name,
            :display_size_mm,
            :display_type,
            :resolution_h_px,
            :resolution_w_px,
            :ram_bytes,
            :rom_bytes,
            :os_id,
            :connectivity,
            :battery_life_mah,
            :water_resistance_value,
            :water_resistance_unit,
            :sensor,
            :case_material,
            :band_material,
            :band_size_mm,
            :band_color,
            :weight_milligrams,
            :release_date,
            :stop_selling
        )";

        $stmt = $this->conn->prepare($sql);

        // Bind các giá trị
        $this->bindVariationValues($stmt, $data);

        $stmt->execute();

        return $this->get($this->conn->lastInsertId());
    }

    public function getAll(?int $limit, ?int $offset): array | false
    {
        $sql = "SELECT * FROM product_variations";

        if ($limit && $offset) {
            $sql .= " LIMIT :limit OFFSET :offset";
        } elseif ($limit) {
            $sql .= " LIMIT :limit";
        } elseif ($offset) {
            $sql .= " LIMIT 18446744073709551615 OFFSET :offset";
        }

        $stmt = $this->conn->prepare($sql);
        if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get(int $id): array | false
    {
        $sql = "SELECT * FROM product_variations WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByProductId(int $productId, ?int $limit, ?int $offset): array | false
    {
        $sql = "SELECT * FROM product_variations WHERE product_id = :product_id";

        if ($limit && $offset) {
            $sql .= " LIMIT :limit OFFSET :offset";
        } elseif ($limit) {
            $sql .= " LIMIT :limit";
        } elseif ($offset) {
            $sql .= " LIMIT 18446744073709551615 OFFSET :offset";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":product_id", $productId, PDO::PARAM_INT);
        if ($limit) $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        if ($offset) $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(array $current, array $new): array | false
    {
        $oldImage = $current["image_name"];

        // Xử lý ảnh mới
        if (!empty($new["image_base64"])) {
            $newImage = $this->saveBase64Image($new["image_base64"]);
            if ($newImage === false) {
                return false;
            }
            $new["image_name"] = $newImage;

            // Xóa ảnh cũ nếu không phải ảnh mặc định
            if ($oldImage !== "default.webp") {
                $this->deleteImage($oldImage);
            }
        } else {
            $new["image_name"] = $oldImage;
        }

        // Xử lý số lượng tồn kho
        $currentStock = $current["stock_quantity"];
        $stockChange = $new["stock_quantity"] ?? 0;

        if (isset($new["stock_quantity"])) {
            $newStock = $currentStock + $stockChange;
            if ($newStock < 0) {
                throw new Exception("Số lượng tồn kho không thể âm");
            }
            $new["stock_quantity"] = $newStock;
        } else {
            $new["stock_quantity"] = $currentStock;
        }

        $sql = "UPDATE product_variations SET
            product_id = :product_id,
            watch_size_mm = :watch_size_mm,
            watch_color = :watch_color,
            price_cents = :price_cents,
            base_price_cents = :base_price_cents,
            image_name = :image_name,
            display_size_mm = :display_size_mm,
            display_type = :display_type,
            resolution_h_px = :resolution_h_px,
            resolution_w_px = :resolution_w_px,
            ram_bytes = :ram_bytes,
            rom_bytes = :rom_bytes,
            os_id = :os_id,
            connectivity = :connectivity,
            battery_life_mah = :battery_life_mah,
            water_resistance_value = :water_resistance_value,
            water_resistance_unit = :water_resistance_unit,
            sensor = :sensor,
            case_material = :case_material,
            band_material = :band_material,
            band_size_mm = :band_size_mm,
            band_color = :band_color,
            weight_milligrams = :weight_milligrams,
            release_date = :release_date,
            stop_selling = :stop_selling,
            stock_quantity = :stock_quantity    
            WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $this->bindVariationValues($stmt, array_merge($current, $new));
        $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);

        $stmt->execute();
        return $this->get($current["id"]);
    }

    public function delete(int $id): bool
    {
        $sql = $this->hasConstraint($id)
            ? "UPDATE product_variations SET stop_selling = true WHERE id = :id"
            : "DELETE FROM product_variations WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getLatestId(): int | false
    {
        $sql = "SELECT MAX(id) FROM product_variations";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countAll(): int
    {
        $sql = "SELECT COUNT(*) FROM product_variations";
        $stmt = $this->conn->query($sql);
        return (int) $stmt->fetchColumn();
    }

    public function getVariationsByFiltersWithPagination(
        ?int $id,
        ?int $product_id,
        ?int $price_cents_min,
        ?int $price_cents_max,
        ?int $os_id,
        ?bool $stop_selling,
        ?int $limit,
        ?int $offset
    ): array {
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM product_variations WHERE 1=1";
        $params = [];

        // Thêm các điều kiện lọc
        if ($id !== null) {
            $sql .= " AND id = :id";
            $params[':id'] = $id;
        }

        if ($product_id !== null) {
            $sql .= " AND product_id = :product_id";
            $params[':product_id'] = $product_id;
        }

        // Xử lý lọc giá
        if ($price_cents_min !== null || $price_cents_max !== null) {
            if ($price_cents_min !== null && $price_cents_max !== null && $price_cents_min > $price_cents_max) {
                http_response_code(400);
                return [
                    'data' => [],
                    'total' => 0,
                    'error' => 'Invalid price range: Minimum price cannot be greater than maximum price',
                    'validation_errors' => [
                        'price_cents_min' => $price_cents_min,
                        'price_cents_max' => $price_cents_max
                    ]
                ];
            }

            if ($price_cents_min !== null && $price_cents_max === null) {
                $sql .= " AND price_cents >= :price_cents_min";
                $params[':price_cents_min'] = $price_cents_min;
            } elseif ($price_cents_max !== null && $price_cents_min === null) {
                $sql .= " AND price_cents <= :price_cents_max";
                $params[':price_cents_max'] = $price_cents_max;
            } elseif ($price_cents_min !== null && $price_cents_max !== null) {
                $sql .= " AND price_cents BETWEEN :price_cents_min AND :price_cents_max";
                $params[':price_cents_min'] = $price_cents_min;
                $params[':price_cents_max'] = $price_cents_max;
            }
        }

        if ($os_id !== null) {
            $sql .= " AND os_id = :os_id";
            $params[':os_id'] = $os_id;
        }

        if ($stop_selling !== null) {
            $sql .= " AND stop_selling = :stop_selling";
            $params[':stop_selling'] = (int)$stop_selling;
        }

        $sql .= " ORDER BY id DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params[':limit'] = $limit;

            if ($offset !== null) {
                $sql .= " OFFSET :offset";
                $params[':offset'] = $offset;
            }
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $total = $this->conn->query("SELECT FOUND_ROWS()")->fetchColumn();

        return [
            'data' => $data,
            'total' => (int)$total
        ];
    }

    public function getFlattenedVariationInstancesWithPagination(
        ?int $id,
        ?int $product_id,
        ?int $price_cents_min,
        ?int $price_cents_max,
        ?int $os_id,
        ?bool $stop_selling,
        ?int $limit,
        ?int $offset
    ): array {
        $rawVariations = $this->getVariationsByFiltersWithPagination(
            $id,
            $product_id,
            $price_cents_min,
            $price_cents_max,
            $os_id,
            $stop_selling,
            null,
            null
        );

        if (empty($rawVariations['data'])) {
            return [
                'total' => 0,
                'data' => []
            ];
        }

        $db = new Database("localhost:3306", "webphp_ec", "root", "123456");
        $instanceGateway = new ProductInstanceGateway($db);
        $flattened = [];

        foreach ($rawVariations['data'] as $variation) {
            $instances = $instanceGateway->getByProductVariationIdWithPagination(
                $variation['id'],
                1000,
                0
            );

            $hasInventory = !empty($instances['data']);
            $inventoryStatus = $hasInventory ? 'in_stock' : 'not_received';

            if ($hasInventory) {
                foreach ($instances['data'] as $instance) {
                    $flattened[] = $this->createFlattenedItem($variation, $instance, $inventoryStatus);
                }
            } else {
                $flattened[] = $this->createFlattenedItem($variation, null, $inventoryStatus);
            }
        }

        $total = count($flattened);
        $pagedData = array_slice($flattened, $offset ?? 0, $limit ?? $total);

        return [
            'total' => $total,
            'data' => $pagedData
        ];
    }

    private function createFlattenedItem(array $variation, ?array $instance, string $inventoryStatus): array
    {
        $item = [
            'inventory_status' => $inventoryStatus,
            'id' => $variation['id'],
            'product_id' => $variation['product_id'],
        ];

        if ($instance !== null) {
            $item['sku'] = $instance['sku'];
            $item['instance_info'] = [
                'is_sold' => $instance['is_sold'],
                'goods_receipt_note_id' => $instance['goods_receipt_note_id'],
                'created_at' => $instance['created_at'] ?? null
            ];
        } else {
            $item['sku'] = null;
            $item['instance_info'] = null;
        }

        $item += [
            'watch_size_mm' => $variation['watch_size_mm'],
            'watch_color' => $variation['watch_color'],
            'display_size_mm' => $variation['display_size_mm'],
            'display_type' => $variation['display_type'],
            'price_cents' => $variation['price_cents'],
            'base_price_cents' => $variation['base_price_cents'],
            'stock_quantity' => $variation['stock_quantity'],
            'stop_selling' => $variation['stop_selling'],
            'image_url' => $this->getImageUrl($variation['image_name']),
            'ram_bytes' => $variation['ram_bytes'],
            'rom_bytes' => $variation['rom_bytes'],
            'os_id' => $variation['os_id'],
            'os_version' => $variation['os_version'] ?? null,
            'connectivity' => $variation['connectivity'],
            'battery_life_mah' => $variation['battery_life_mah'],
            'water_resistance' => [
                'value' => $variation['water_resistance_value'],
                'unit' => $variation['water_resistance_unit']
            ],
            'case_material' => $variation['case_material'],
            'band_material' => $variation['band_material'],
            'band_color' => $variation['band_color'],
            'weight_milligrams' => $variation['weight_milligrams'],
            'release_date' => $variation['release_date'],
            'warranty_months' => $variation['warranty_months'] ?? 12,
            'features' => [
                'is_waterproof' => $variation['is_waterproof'] ?? false,
                'has_gps' => $variation['has_gps'] ?? false,
                'has_heart_rate' => str_contains($variation['sensor'] ?? '', 'heart_rate'),
                'has_spo2' => str_contains($variation['sensor'] ?? '', 'spo2')
            ]
        ];

        return $item;
    }

    private function bindVariationValues(PDOStatement $stmt, array $data): void
    {
        $stmt->bindValue(":product_id", $data["product_id"], PDO::PARAM_INT);
        $stmt->bindValue(":watch_size_mm", $data["watch_size_mm"], PDO::PARAM_INT);
        $stmt->bindValue(":watch_color", $data["watch_color"], PDO::PARAM_STR);
        $stmt->bindValue(":price_cents", $data["price_cents"] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":base_price_cents", $data["base_price_cents"] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(":image_name", $data["image_name"] ?? "default.webp", PDO::PARAM_STR);
        $stmt->bindValue(":display_size_mm", $data["display_size_mm"], PDO::PARAM_INT);
        $stmt->bindValue(":display_type", $data["display_type"], PDO::PARAM_STR);
        $stmt->bindValue(":resolution_h_px", $data["resolution_h_px"], PDO::PARAM_INT);
        $stmt->bindValue(":resolution_w_px", $data["resolution_w_px"], PDO::PARAM_INT);
        $stmt->bindValue(":ram_bytes", $data["ram_bytes"], PDO::PARAM_INT);
        $stmt->bindValue(":rom_bytes", $data["rom_bytes"], PDO::PARAM_INT);
        $stmt->bindValue(":os_id", $data["os_id"], PDO::PARAM_INT);
        $stmt->bindValue(":connectivity", $data["connectivity"], PDO::PARAM_STR);
        $stmt->bindValue(":battery_life_mah", $data["battery_life_mah"], PDO::PARAM_INT);
        $stmt->bindValue(":water_resistance_value", $data["water_resistance_value"], PDO::PARAM_INT);
        $stmt->bindValue(":water_resistance_unit", $data["water_resistance_unit"], PDO::PARAM_STR);
        $stmt->bindValue(":sensor", $data["sensor"], PDO::PARAM_STR);
        $stmt->bindValue(":case_material", $data["case_material"], PDO::PARAM_STR);
        $stmt->bindValue(":band_material", $data["band_material"], PDO::PARAM_STR);
        $stmt->bindValue(":band_size_mm", $data["band_size_mm"], PDO::PARAM_INT);
        $stmt->bindValue(":band_color", $data["band_color"], PDO::PARAM_STR);
        $stmt->bindValue(":weight_milligrams", $data["weight_milligrams"], PDO::PARAM_INT);
        $stmt->bindValue(":release_date", $data["release_date"], PDO::PARAM_STR);
        $stmt->bindValue(":stop_selling", $data["stop_selling"] ?? false, PDO::PARAM_BOOL);
        $stmt->bindValue(":stock_quantity", $data["stock_quantity"] ?? 0, PDO::PARAM_INT);
    }

    private function hasConstraint(int $id): bool
    {
        $sql = "SELECT EXISTS (
            SELECT 1 FROM carts WHERE product_variation_id = :product_variation_id
            UNION
            SELECT 1 FROM product_instances WHERE product_variation_id = :product_variation_id
        )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":product_variation_id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    private function saveBase64Image(string $base64Image): string | false
    {
        $uploadDir = __DIR__ . "/../../../backend/uploads/products/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (strpos($base64Image, 'data:image/') === 0) {
            $format = explode('/', explode(';', $base64Image)[0])[1];
            $imageData = base64_decode(explode(',', $base64Image)[1]);
        } else {
            $format = 'png';
            $imageData = base64_decode($base64Image);
        }

        $allowedFormats = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($format), $allowedFormats)) {
            error_log("Định dạng ảnh không được hỗ trợ: $format");
            return false;
        }

        $imageName = uniqid() . '.' . $format;
        $targetFile = $uploadDir . $imageName;

        if (file_put_contents($targetFile, $imageData)) {
            return $imageName;
        } else {
            error_log("Lỗi khi lưu ảnh từ Base64.");
            return false;
        }
    }

    private function deleteImage(string $imageName): bool
    {
        if ($imageName === "default.webp") {
            return true;
        }

        $filePath = __DIR__ . "/../../../backend/uploads/products/" . $imageName;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    private function getImageUrl(?string $imageName): ?string
    {
        if (!$imageName || $imageName === 'default.webp') {
            return null;
        }
        return BASE_API_URL . "/backend/uploads/products/" . $imageName;
    }

    private function countAvailableInstances(int $variationId): int
    {
        $sql = "SELECT COUNT(*) FROM product_instances 
              WHERE product_variation_id = :variation_id 
              AND is_sold = false";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":variation_id", $variationId, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
}
