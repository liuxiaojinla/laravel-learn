<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Post
 *
 * @package App\Models
 * @mixin \Illuminate\Database\Query\Builder
 */
class Post extends Model{

	/**
	 * 关联用户信息
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user(){
		return $this->belongsTo('App\Models\User', 'uid');
	}
}
