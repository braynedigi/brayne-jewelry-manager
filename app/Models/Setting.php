<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description'
    ];

    /**
     * Get a setting value by key
     */
    public static function getValue($key, $default = null)
    {
        // Try to get from cache first
        $cacheKey = "setting_{$key}";
        $cachedValue = Cache::get($cacheKey);
        
        if ($cachedValue !== null) {
            return $cachedValue;
        }
        
        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;
        
        // Cache the value for 1 hour
        Cache::put($cacheKey, $value, 3600);
        
        return $value;
    }

    /**
     * Set a setting value
     */
    public static function setValue($key, $value, $type = 'string', $group = 'general', $label = null, $description = null)
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'label' => $label ?: ucfirst(str_replace('_', ' ', $key)),
                'description' => $description
            ]
        );
        
        // Clear cache for this setting
        Cache::forget("setting_{$key}");
        
        return $setting;
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get login page settings
     */
    public static function getLoginSettings()
    {
        return [
            'logo' => static::getValue('login_logo'),
            'background_color' => static::getValue('login_background_color', '#f8fafc'),
            'background_image' => static::getValue('login_background_image'),
        ];
    }

    /**
     * Get notification settings
     */
    public static function getNotificationSettings()
    {
        return [
            'email_notifications' => static::getValue('email_notifications', true),
            'in_app_notifications' => static::getValue('in_app_notifications', true),
            'order_notifications' => static::getValue('order_notifications', true),
            'customer_notifications' => static::getValue('customer_notifications', true),
            'product_notifications' => static::getValue('product_notifications', true),
        ];
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        $settings = static::all();
        foreach ($settings as $setting) {
            Cache::forget("setting_{$setting->key}");
        }
        Cache::forget('settings');
    }
}
