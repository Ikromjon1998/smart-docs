<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'title',
        'category',
        'summary',
        'file_paths',
        'page_count',
        'output_format',
        'file_size',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'file_paths' => 'array',
        ];
    }
}
