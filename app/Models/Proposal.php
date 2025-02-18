<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'proposals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_kelompok', 'judul_proposal', 'file_proposal', 'tgl_upload', 'status', 'verifikator', 'keterangan', 'tgl_verifikasi', 'mahasiswa', 'token'];

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

    public function ketuaKelompok()
    {
        return $this->hasOne(Kelompok::class, 'id_kelompok', 'id_kelompok')->where('peran', 'Ketua');
    }
    
    public function kelompoks()
    {
        return $this->hasMany(Kelompok::class, 'id_kelompok', 'id_kelompok');
    }
}
