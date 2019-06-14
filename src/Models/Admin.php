<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:25 PM
 */
namespace Tw\Server\Models;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
class Admin extends Model implements AuthenticatableContract
{
    use Authenticatable;
    /**
     * @var array
     */
    protected $fillable = ['name','password','phone'];
    /**
     * Admin constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('tw.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('tw.database.admin_table'));

        parent::__construct($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activitys()
    {
        return $this->hasMany('Tw\Server\Models\Activity');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pay_orders()
    {
        return $this->hasMany('Tw\Server\Models\PayOrder');
    }

    /**
     * @return string
     */
    public function getIndexUrl():string
    {
        return route('tw.userinfo');
    }
}