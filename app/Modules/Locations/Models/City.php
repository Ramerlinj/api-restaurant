<?php

namespace App\Modules\Locations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $keyType = 'int';

    /**
     * @var string
     */
    protected $table = 'cities';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
    ];
}
