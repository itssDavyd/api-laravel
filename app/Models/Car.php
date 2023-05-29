<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    //En caso de querer actualizar o realizar cualquier tipo de CRUD contra este MODELO debemos ponerle los fillable para que tenga como referencia de lo que es.
    protected $fillable = ['title', 'description', 'price', 'status'];

    protected $table = 'cars';

    //Relacion de muchos a uno.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
