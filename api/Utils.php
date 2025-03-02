<?php

class Utils {

  public function genProductSKU(
    string $category,
    string $brand,
    string $model,
    string $watch_size_mm,
    string $watch_color,
    string $band_material
  ): string { //cate-brand-model-watch_size-color-band_mater: SW-APL-S8-45-BLK-SIL-TIME_MILI

    $category = trim($category);
    $brand = trim($brand);
    $model = trim($model);
    $watch_size = trim($watch_size_mm);
    $watch_color = trim($watch_color);
    $band_material = trim($band_material);
    $milisecs = (string) $this->getCurrentTime(); //make sure unique

    return strtolower(implode('-', [$category, $brand, $model, $watch_size, $watch_color, $band_material, $milisecs]));
  }

  public function isValidProductSKU(string $sku): bool { //AI gen
    // Regular expression to match the SKU format
    $pattern = '/^[a-z]+-[a-z]+-[a-z0-9]+-[0-9]+-[a-z]+-[a-z]+-[0-9]+$/'; // Improved regex

    return preg_match($pattern, $sku);
  }

  public function getCurrentTime(): float { // return milisecs
    return round(microtime(true) * 1000);
  }

  public function isValidDateTimeFormat(string $dateTimeString): bool { //AI gen
    $format = 'Y-m-d H:i:s'; // The format you want to check

    try {
      $dateTime = DateTime::createFromFormat($format, $dateTimeString);

      // Check if the DateTime object was created successfully AND if the formatted date matches the original string
      return $dateTime && $dateTime->format($format) === $dateTimeString;

    } catch (Exception $e) {
      return false; // Invalid date/time string or format mismatch
    }
  }

  public function isValidEmail(string $email): bool {
    // Remove all illegal characters from email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate e-mail
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
  }
  public function isValidEmailRobust(string $email): bool { //AI gen: More robust, but potentially overkill (and slower):
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);  // Sanitize first

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

    // Check for DNS records (MX or A) - more thorough but slower
    $domain = explode('@', $email)[1];
    return (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')) ? true : false;
  }

  public function isValidVNPhoneNumber(string $phoneNumber): bool { //AI gen
    // 1. Remove all non-numeric characters (except the leading '+')
    $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

    // 2. Check for the leading '+' (for international numbers)
    $isInternational = str_starts_with($phoneNumber, '+');

    $isInternational
      ? $pattern = '/^\+84[0-9]{9}$/' //International format (e.g., +84901234567), +84 followed by 9 digits
      : $pattern = '/^0[0-9]{9}$/'; //Domestic format (e.g., 0901234567 or 0321234567), 0 followed by 9 digits


    // 3. Perform the regex match
    return (bool) preg_match($pattern, $phoneNumber);
  }

  public function isValidPassword(string $password, int $minLength=8): bool { //AI support
    // password contain at least a letter or a number and length >= minLength -> valid
    return (strlen($password) >= $minLength && preg_match('/[a-zA-Z0-9]/', $password)) ? true : false;
  }
}
