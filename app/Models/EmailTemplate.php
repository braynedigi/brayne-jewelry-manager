<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'type',
        'is_active',
        'description'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get template by name
     */
    public static function getByName($name)
    {
        return static::where('name', $name)->where('is_active', true)->first();
    }

    /**
     * Get template by type
     */
    public static function getByType($type)
    {
        return static::where('type', $type)->where('is_active', true)->first();
    }

    /**
     * Replace variables in template content
     */
    public function render($data = [])
    {
        $content = $this->content;
        $subject = $this->subject;

        // Replace variables in content and subject
        foreach ($data as $key => $value) {
            $placeholder = "{{" . $key . "}}";
            $content = str_replace($placeholder, $value, $content);
            $subject = str_replace($placeholder, $value, $subject);
        }

        return [
            'subject' => $subject,
            'content' => $content
        ];
    }

    /**
     * Get available variables for this template
     */
    public function getAvailableVariables()
    {
        return $this->variables ?? [];
    }

    /**
     * Scope for active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for templates by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
