<?php

class Coupon
{
    private $db;
    private $data = [];

    public function __construct($database = null, $data = [])
    {
        $this->db = $database;
        $this->data = $data;
    }

    /**
     * Find coupon by name
     */
    public static function findByName($name)
    {
        $db = Database::getInstance();

        $stmt = $db->prepare("SELECT * FROM coupons WHERE UPPER(name) = UPPER(?)");
        $stmt->execute([$name]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new self($db, $result);
        }

        return null;
    }

    /**
     * Check if coupon is valid (not expired)
     */
    public function isValid()
    {
        if (empty($this->data['valid_until'])) {
            return false;
        }

        $validUntil = strtotime($this->data['valid_until']);
        $now = time();

        return $validUntil > $now;
    }

    /**
     * Magic getter for accessing coupon properties
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Get coupon as array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get coupon data as JSON (with discount renamed to discount_amount for API)
     */
    public function toApiArray()
    {
        return [
            'id' => $this->data['id'] ?? null,
            'name' => $this->data['name'] ?? null,
            'discount_amount' => $this->data['discount'] ?? 0,
            'valid_until' => $this->data['valid_until'] ?? null,
        ];
    }
}
