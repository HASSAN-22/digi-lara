<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    private static string $prefix = 'App\Notifications\\';

    private static string $notificationName;

    private static $queryBuilder;

    /**
     * Get notification class name
     * @param string $notificationName
     * @return NotificationService
     */
    public static function name(string $notificationName):NotificationService{
        self::$notificationName = self::$prefix . $notificationName;
        self::$queryBuilder = DB::table('notifications')->where('type',self::$notificationName);
        return new self();
    }

    /**
     * Find notifications for specific user
     * @param int $userId
     * @param bool $isAdmin
     * @return NotificationService
     */
    public static function forUser(int $userId, bool $isAdmin = false):NotificationService{
        if($isAdmin){
            $userIds = User::where('access','admin')->get()->pluck('id')->toArray();
            self::query()->whereIn('notifiable_id', $userIds);
        }else{
            self::query()->where('notifiable_id', $userId);
        }
        return new self();
    }

    /**
     * Send a notification to user or admins
     * @param int $postId
     * @param User|null $user
     * @param bool $toAdmin
     * @param bool $toUser
     * @return void
     */
    public static function send(int $postId, User|null $user, bool $toAdmin=true, bool $toUser=true, $key=null):void{
        if(!is_null($key)){
            self::delete($postId, $key);
        }
        if($user and $toUser){
            $user->notify(new self::$notificationName($postId));
        }
        if($toAdmin){
            User::where('access','admin')->get()->map(fn($user)=>$user->notify(new self::$notificationName($postId)));
        }
    }

    /**
     * Delete specific notification(s)
     * @param int|null $postId
     * @param string|null $pattern
     * @return void
     */
    public static function delete(int|null $postId=null, string|null $pattern=null):void{
        self::query($postId, $pattern)->delete();
    }

    /**
     * Get all notifications
     * @param int|null $postId
     * @param string|null $pattern
     * @return Collection
     */
    public static function get(int|null $postId=null, string|null $pattern=null): Collection
    {
        return self::query($postId, $pattern)->get();
    }

    /**
     * Get unread notifications
     * @param int|null $postId
     * @param string|null $pattern
     * @return Collection
     */
    public static function getUnRead(): Collection
    {
        $query = str_replace(array('?'), array('\'%s\''), self::query()->toSql());
        $query = vsprintf($query, self::query()->getBindings());
        return self::query()->whereNull('read_at')->get();
    }

    /**
     * Read specific notification(s)
     * @param int|null $postId
     * @param string|null $pattern
     * @return Builder
     */
    public static function read(int|null $postId=null, string|null $pattern=null):Builder{
        self::query($postId, $pattern)->update(['read_at'=>now()]);
        return self::$queryBuilder;
    }


    /**
     * create query for find current notification
     * @return Builder
     */
    private static function query(int|null $postId=null, string|null $pattern=null): Builder{
        if($postId and $pattern){
            self::$queryBuilder = self::$queryBuilder->whereJsonContains($pattern,$postId);
        }
        return self::$queryBuilder;
    }
}
