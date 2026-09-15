<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['setting_key', 'setting_value', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil nilai pengaturan berdasarkan setting_key.
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public function getByKey(string $key, ?string $default = null): ?string
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Menyimpan atau memperbarui nilai pengaturan berdasarkan setting_key.
     *
     * @param string $key
     * @param string|null $value
     * @return bool
     */
    public function setByKey(string $key, ?string $value): bool
    {
        $existing = $this->where('setting_key', $key)->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->insert([
            'setting_key'   => $key,
            'setting_value' => $value,
        ]);
    }
}