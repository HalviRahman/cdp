<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Proposal;
use Illuminate\Support\Facades\DB;

class ProgramStudi extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'program_studis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nama_prodi', 'kuota', 'jenjang', 'tahun'];

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

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'prodi', DB::raw("CONCAT(jenjang, ' ', nama_prodi)"));
    }

    // Accessor untuk mendapatkan nama prodi lengkap
    public function getProdiFullAttribute()
    {
        return $this->jenjang . ' ' . $this->nama_prodi;
    }
}
