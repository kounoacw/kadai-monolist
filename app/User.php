<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    /**
     * リレーション(多対多) users , items
     * 
     */
    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }
    
    /**
     * type = 'want'のアイテム一覧を取得
     * 
     */
    public function want_items()
    {
        return $this->items()->where('type', 'want');
    }
    
    /**
     * WANTした際に中間テーブルにデータを作成する
     * 
     */
    public function want($itemId)
    {
        // 既に Wantしているかの確認
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            // 既にWantしていれば何もしない
            return false;
        } else {
            // 未 Want であれば Wantする
            $this->items()->attach($itemId, ['type' => 'want']);
            return true;
        }
    }
    
    /**
     * WANTを外す
     * 
     */
    public function dont_want($itemId)
    {
        // 既に Wantしているかの確認
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            // 既にWantしていれば Want をはずす
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [\Auth::user()->id, $itemId]);
        } else {
            // 未 Want であれば何もしない
            return false;
        }
    }
    
    /**
     * Want しているかを確認
     * 
     */
    public function is_wanting($itemIdOrCode)
    {
        // 数値の場合、item_idとみなす
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->want_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
    
    /**
     * type = 'have' のアイテム一覧を取得
     * 
     */
    public function have_items()
    {
        return $this->items()->where('type', 'have');
    }
    
    /**
     * Have した際に中間テーブルにデータを作成する
     * 
     */
    public function have($itemId)
    {
        // 既に Haveしているかの確認
        $exist = $this->is_having($itemId);
        
        if ($exist) {
            // 既にHaveしていれば何もしない
            return false;
        } else {
            // 未 Have であれば Haveする
            $this->items()->attach($itemId, ['type' => 'have']);
            return true;
        }
    }
    
    /**
     * HAVEをはずす
     * 
     */
    public function dont_have($itemId)
    {
        // 既に Haveしているかの確認
        $exist = $this->is_having($itemId);
        
        if ($exist) {
            // 既にHaveしていれば Have をはずす
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [\Auth::user()->id, $itemId]);
        } else {
            // 未 Have であれば何もしない
            return false;
        }
    }
    
    /**
     * Have しているかを確認
     * 
     */
    public function is_having($itemIdOrCode)
    {
        // 数値の場合、item_idとみなす
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_code_exists;
        }
    }
}
