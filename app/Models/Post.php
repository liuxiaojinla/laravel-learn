<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @property string title
 * @method static findOrFail($filter = []) static
 * @mixin \Illuminate\Database\Query\Builder
 */
class Post extends Model{

    //	/**
    //	 * 可以被批量赋值的属性。
    //	 *
    //	 * @var array
    //	 */
    //	protected $fillable = [
    //		'title', 'keywords', 'description', 'content',
    //	];

    /**
     * 不被写入的字段
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 关联用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(){
        return $this->belongsTo('App\Models\User', 'uid');
    }
}
