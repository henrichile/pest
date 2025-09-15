<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model
{
    use HasFactory;

    protected  = [
        'title',
        'description',
        'type',
        'options',
        'is_required',
        'order',
        'checklist_template_id',
    ];

    protected  = [
        'is_required' => 'boolean',
        'options' => 'array',
    ];

    public function template()
    {
        return ->belongsTo(ChecklistTemplate::class);
    }
}
