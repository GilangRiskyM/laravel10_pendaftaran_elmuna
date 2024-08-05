<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komputer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'daftar_komputer';

    protected $fillable = [
        'nik',
        'nama',
        'alamat',
        'tempat_lahir',
        'tanggal_lahir',
        'jk',
        'nama_ayah',
        'nama_ibu',
        'telepon',
        'email',
        'kecamatan',
        'paket',
        'tanggal'

    ];
}
