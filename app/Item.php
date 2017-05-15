<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['code', 'name', 'url', 'image_url'];
    
    /**
     * リレーション(多対多) items , users
     *
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('type')->withTimestamps();
    }
    
    /**
     * type = 'want'のユーザ一覧を取得
     * 
     */
    public function want_users()
    {
        return $this->users()->where('type', 'want');
    }
    
    /**
     * type = 'have'のユーザ一覧を取得
     * 
     */
    public function have_users()
    {
        return $this->users()->where('type', 'have');
    }
}
