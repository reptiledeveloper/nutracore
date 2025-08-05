<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model{

    protected $table = 'categories';

    protected $guarded = ['id'];



    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relationship for subcategories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
