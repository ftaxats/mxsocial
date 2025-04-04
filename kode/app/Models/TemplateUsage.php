<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Filterable;
class TemplateUsage extends Model
{
    use HasFactory , Filterable;

    protected $guarded = [];


    protected $casts = [
        'open_ai_usage' => 'object',
    ];


    /**
     * Get the user that owns the TemplateUsage
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get the admin that owns the TemplateUsage
     *
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }


    /**
     * Get the template that owns the TemplateUsage
     *
     * @return BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(AiTemplate::class, 'template_id')->withDefault([
            'name' => '-'
        ]);
    }
}
