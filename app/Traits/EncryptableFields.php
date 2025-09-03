<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait EncryptableFields
{
    /**
     * Klinikyazilim özel şifreleme anahtarı
     * Bu anahtar sadece bu sisteme özgüdür
     */
    private static $encryptionKey = 'klinikyazilim_2025_secure_key_v1';
    
    /**
     * Şifrelenecek alanları tanımla
     * Bu dizi her modelde tanımlanmalıdır
     * Örnek: protected $encryptable = ['field1', 'field2'];
     */
    
    /**
     * Model boot edilirken şifreleme işlemlerini ayarla
     */
    protected static function bootEncryptableFields()
    {
        static::saving(function ($model) {
            $model->encryptFields();
        });
        
        static::retrieved(function ($model) {
            $model->decryptFields();
        });
    }
    
    /**
     * Alanları şifrele
     */
    protected function encryptFields()
    {
        foreach ($this->encryptable as $field) {
            if (!empty($this->attributes[$field])) {
                $this->attributes[$field] = $this->encryptValue($this->attributes[$field]);
            }
        }
    }
    
    /**
     * Alanları çöz
     */
    protected function decryptFields()
    {
        foreach ($this->encryptable as $field) {
            if (!empty($this->attributes[$field])) {
                $this->attributes[$field] = $this->decryptValue($this->attributes[$field]);
            }
        }
    }
    
    /**
     * Değeri şifrele
     */
    private function encryptValue($value)
    {
        try {
            // Klinikyazilim özel şifreleme
            $encrypted = base64_encode(
                openssl_encrypt(
                    $value,
                    'AES-256-CBC',
                    hash('sha256', self::$encryptionKey),
                    0,
                    substr(hash('sha256', self::$encryptionKey), 0, 16)
                )
            );
            
            // Klinikyazilim prefix'i ekle
            return 'KLINIK_' . $encrypted;
        } catch (\Exception $e) {
            return $value; // Şifreleme başarısız olursa orijinal değeri döndür
        }
    }
    
    /**
     * Değeri çöz
     */
    private function decryptValue($value)
    {
        try {
            // Klinikyazilim prefix'ini kontrol et
            if (!str_starts_with($value, 'KLINIK_')) {
                return $value; // Şifrelenmemiş değer
            }
            
            // Prefix'i kaldır
            $encrypted = substr($value, 7);
            
            // Şifreyi çöz
            $decrypted = openssl_decrypt(
                base64_decode($encrypted),
                'AES-256-CBC',
                hash('sha256', self::$encryptionKey),
                0,
                substr(hash('sha256', self::$encryptionKey), 0, 16)
            );
            
            return $decrypted ?: $value;
        } catch (\Exception $e) {
            return $value; // Çözme başarısız olursa orijinal değeri döndür
        }
    }
    
    /**
     * Belirli bir alanı manuel olarak şifrele
     */
    public function encryptField($field, $value)
    {
        return $this->encryptValue($value);
    }
    
    /**
     * Belirli bir alanı manuel olarak çöz
     */
    public function decryptField($field, $value)
    {
        return $this->decryptValue($value);
    }
    
    /**
     * Şifrelenmiş arama için
     */
    public function searchEncrypted($field, $value)
    {
        $encrypted = $this->encryptValue($value);
        return $this->where($field, $encrypted);
    }
}