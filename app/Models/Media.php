<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Gdevilbat\SpardaCMS\Modules\Core\Entities\User;

class Media extends Model
{
    protected $table = 'gallery';
    protected $primaryKey = 'id_gallery';
    protected $casts = [
        'gallery_source' => 'array',
    ];

    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISH = 'publish';

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
