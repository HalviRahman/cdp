<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelompoks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_kelompok', 'anggota_email', 'peran'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * some columns model type
     *
     * @var array
     */
    const TYPES = [];

    /**
     * Default with relationship
     *
     * @var array
     */
    protected $with = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'anggota_email', 'email');
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_kelompok', 'id_kelompok');
    }
}
