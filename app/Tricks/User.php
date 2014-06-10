<?php

namespace Tricks;

use Eloquent;
use Gravatar;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

	/**
	* Users should not be admin by default
	*
	* @var array
	*/
	protected $attributes = [
		'is_admin' => false
	];

    public function getPresenter()
    {
        return $presenter = 'Tricks\Presenters\UserPresenter';
    }

	/**
	 * Query the user's social profile.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function profile()
	{
		return $this->hasOne('Tricks\Profile');
	}

	/**
	 * Query the tricks that the user has posted.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function tricks()
	{
		return $this->hasMany('Tricks\Trick');
	}

	/**
	 * Query the votes that are casted by the user.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function votes()
	{
		return $this->belongsToMany('Tricks\Trick', 'votes');
	}

	/**
	 * Get the user's avatar image.
	 *
	 * @return string
	 */
	public function getPhotocssAttribute()
	{
		if($this->photo) {
			return url('img/avatar/' . $this->photo);
		}

		return Gravatar::src($this->email, 100);
	}

	/**
	 * Check user's permissions
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return ($this->is_admin == true);
	}

}
