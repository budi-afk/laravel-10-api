<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ["image", "title", "body"];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => asset("storage/books/" . $image),
        );
    }
}
