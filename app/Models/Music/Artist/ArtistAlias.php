<?php

namespace App\Models\Music\Artist;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ArtistAlias extends BaseModel
{
    use HasFactory;

    protected $table = "music_artists_aliases";

    protected $hidden = [
        "row_id", "artist_id", BaseModel::CREATED_AT, BaseModel::UPDATED_AT,
        BaseModel::DELETED_AT, BaseModel::STAMP_DELETED, BaseModel::STAMP_DELETED_BY,
    ];

    protected $primaryKey = "row_id";

    protected string $uuid = "row_uuid";

    protected $guarded = [];
}
