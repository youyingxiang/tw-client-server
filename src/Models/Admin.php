<?php
/**
 * Created by PhpStorm.
 * User: youxingxiang
 * Date: 2019/5/29
 * Time: 7:25 PM
 */
namespace Tw\Server\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
class Admin extends Model implements AuthenticatableContract
{
    use Authenticatable;
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
}