<?php

class ProductVariationGateway
{
  private PDO $conn;

  public function __construct(Database $db)
  {
    $this->conn = $db->getConnection();
  }

  public function create(array $data): array | false
  { //TODO auto gen product_instances
    $imageFileName = null;

    // Nếu có ảnh Base64, lưu vào thư mục và lấy tên file
    if (!empty($data["image_base64"])) {
      $imageFileName = $this->saveBase64Image($data["image_base64"]);
      if ($imageFileName === false) {
        return false;
      }
      $data["image_name"] = $imageFileName;
    } else {
      // Nếu không có ảnh, dùng ảnh mặc định
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
    $stmt->execute();

    return $this->get($this->conn->lastInsertId());
  }

  public function getAll(?int $limit, ?int $offset): array | false
  {
    if ($limit && $offset) {
      $sql = "SELECT * FROM product_variations LIMIT :limit OFFSET :offset";
    } elseif ($limit) {
      $sql = "SELECT * FROM product_variations LIMIT :limit";
    } elseif ($offset) {
      $sql = "SELECT * FROM product_variations LIMIT 18446744073709551615 OFFSET :offset";
    } else {
      $sql = "SELECT * FROM product_variations";
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
    $stockChange = $new["stock_quantity"] ?? 0; // Số lượng thay đổi
    // Nếu có yêu cầu thay đổi số lượng
    if (isset($new["stock_quantity"])) {
      $newStock = $currentStock + $stockChange; // Cộng dồn
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
      WHERE id = :id
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":product_id", $new["product_id"] ?? $current["product_id"], PDO::PARAM_INT);
    $stmt->bindValue(":watch_size_mm", $new["watch_size_mm"] ?? $current["watch_size_mm"], PDO::PARAM_INT);
    $stmt->bindValue(":watch_color", $new["watch_color"] ?? $current["watch_color"], PDO::PARAM_STR);
    $stmt->bindValue(":price_cents", $new["price_cents"] ?? $current["price_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":base_price_cents", $new["base_price_cents"] ?? $current["base_price_cents"], PDO::PARAM_INT);
    $stmt->bindValue(":image_name", $new["image_name"], PDO::PARAM_STR);
    $stmt->bindValue(":display_size_mm", $new["display_size_mm"] ?? $current["display_size_mm"], PDO::PARAM_INT);
    $stmt->bindValue(":display_type", $new["display_type"] ?? $current["display_type"], PDO::PARAM_STR);
    $stmt->bindValue(":resolution_h_px", $new["resolution_h_px"] ?? $current["resolution_h_px"], PDO::PARAM_INT);
    $stmt->bindValue(":resolution_w_px", $new["resolution_w_px"] ?? $current["resolution_w_px"], PDO::PARAM_INT);
    $stmt->bindValue(":ram_bytes", $new["ram_bytes"] ?? $current["ram_bytes"], PDO::PARAM_INT);
    $stmt->bindValue(":rom_bytes", $new["rom_bytes"] ?? $current["rom_bytes"], PDO::PARAM_INT);
    $stmt->bindValue(":os_id", $new["os_id"] ?? $current["os_id"], PDO::PARAM_INT);
    $stmt->bindValue(":connectivity", $new["connectivity"] ?? $current["connectivity"], PDO::PARAM_STR);
    $stmt->bindValue(":battery_life_mah", $new["battery_life_mah"] ?? $current["battery_life_mah"], PDO::PARAM_INT);
    $stmt->bindValue(":water_resistance_value", $new["water_resistance_value"] ?? $current["water_resistance_value"], PDO::PARAM_INT);
    $stmt->bindValue(":water_resistance_unit", $new["water_resistance_unit"] ?? $current["water_resistance_unit"], PDO::PARAM_STR);
    $stmt->bindValue(":sensor", $new["sensor"] ?? $current["sensor"], PDO::PARAM_STR);
    $stmt->bindValue(":case_material", $new["case_material"] ?? $current["case_material"], PDO::PARAM_STR);
    $stmt->bindValue(":band_material", $new["band_material"] ?? $current["band_material"], PDO::PARAM_STR);
    $stmt->bindValue(":band_size_mm", $new["band_size_mm"] ?? $current["band_size_mm"], PDO::PARAM_INT);
    $stmt->bindValue(":band_color", $new["band_color"] ?? $current["band_color"], PDO::PARAM_STR);
    $stmt->bindValue(":weight_milligrams", $new["weight_milligrams"] ?? $current["weight_milligrams"], PDO::PARAM_INT);
    $stmt->bindValue(":release_date", $new["release_date"] ?? $current["release_date"], PDO::PARAM_STR);
    $stmt->bindValue(":stop_selling", $new["stop_selling"] ?? $current["stop_selling"], PDO::PARAM_BOOL);
    $stmt->bindValue(":stock_quantity", $new["stock_quantity"], PDO::PARAM_INT);
    $stmt->bindValue(":id", $current["id"], PDO::PARAM_INT);
    $stmt->execute();

    return $this->get($current["id"]);
  }
  private function deleteImage(string $imageName): bool
  {
    if ($imageName === "default.webp") {
      return true; // Không xóa ảnh mặc định
    }

    $filePath = __DIR__ . "/../../../backend/uploads/products/" . $imageName;
    if (file_exists($filePath)) {
      return unlink($filePath);
    }
    return false;
  }
  public function delete(int $id): bool
  {
    $sql = $this->hasConstrain($id)
      ? "UPDATE product_variations SET stop_selling = true WHERE id = :id"
      : "DELETE FROM product_variations WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    return $stmt->execute();
  }

  // API lấy product_variation có id lớn nhất để tự động tạo id cho product_instance
  public function getLatestId(): int | false
  {
    $sql = "SELECT MAX(id) FROM product_variations";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchColumn();
  }

  private function hasConstrain(int $id): bool
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

  // private function saveBase64Image(string $base64Image): string | false
  // {
  //   $uploadDir = __DIR__ . "/../../../backend/uploads/products/";
  //   if (!is_dir($uploadDir)) {
  //     mkdir($uploadDir, 0777, true); // Tạo thư mục nếu chưa tồn tại
  //   }

  //   // Tạo tên file duy nhất
  //   $imageName = uniqid() . ".png"; // Mặc định lưu dưới dạng PNG
  //   $targetFile = $uploadDir . $imageName;

  //   // Giải mã Base64 và lưu file
  //   $imageData = base64_decode($base64Image);
  //   if (file_put_contents($targetFile, $imageData)) {
  //     return $imageName;
  //   } else {
  //     error_log("Lỗi khi lưu ảnh từ Base64.");
  //     return false;
  //   }
  // }
  private function saveBase64Image(string $base64Image): string | false
  {
    $uploadDir = __DIR__ . "/../../../backend/uploads/products/";
    if (!is_dir($uploadDir)) {
      mkdir($uploadDir, 0777, true);
    }

    // Kiểm tra định dạng ảnh
    if (strpos($base64Image, 'data:image/') === 0) {
      $format = explode('/', explode(';', $base64Image)[0])[1];
      $imageData = base64_decode(explode(',', $base64Image)[1]);
    } else {
      // Nếu không có header, mặc định là PNG
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
}
