<?php
// +------------------------------------------------+
// |http://www.cjango.com                           |
// +------------------------------------------------+
// | 修复BUG不是一朝一夕的事情，等我喝醉了再说吧！  |
// +------------------------------------------------+
// | Author: 小陈叔叔 <Jason.Chen>                  |
// +------------------------------------------------+
namespace tools;

use think\Db;

/**
 *
 */
class Follow
{

    protected static $error = null;

    public static function error()
    {
        return self::$error;
    }

    public static function is_subscribed($uid, $fid)
    {
        if ($uid > $fid) {
            $smallId = $fid;
            $bigId   = $uid;
            $type    = 2;
        } else {
            $smallId = $uid;
            $bigId   = $fid;
            $type    = 1;
        }
        $model = Db::name('Follow');
        $map   = [
            'uid' => $smallId,
            'fid' => $bigId,
        ];
        $relation = $model->where($map)->find();
        // 如果有关注关系
        if ($relation && $relation['type'] == 3 || $relation['type'] == $type) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 关注用户
     * @param  integer $uid 发起用户
     * @param  integer $fid 关注用户
     * @return boolean
     */
    public static function subscribe($uid, $fid)
    {
        if ($uid > $fid) {
            $smallId = $fid;
            $bigId   = $uid;
            $type    = 2;
        } else {
            $smallId = $uid;
            $bigId   = $fid;
            $type    = 1;
        }
        $model = Db::name('Follow');
        $map   = [
            'uid' => $smallId,
            'fid' => $bigId,
        ];
        $relation = $model->where($map)->find();
        if (!$relation) {
            $data = [
                'uid'  => $smallId,
                'fid'  => $bigId,
                'type' => $type,
            ];
            return $model->insert($data);
        } else {
            // 如果有关注关系
            if ($relation['type'] == 3 || $relation['type'] == $type) {
                self::$error = '已经关注过此用户';
                return false;
            } else {
                return $model->where($map)->setField('type', 3);
            }
        }
    }

    /**
     * 取消关注用户
     * @param  integer $uid 发起用户
     * @param  integer $fid 关注用户
     * @return boolean
     */
    public static function unsubscribe($uid, $fid)
    {
        if ($uid > $fid) {
            $smallId = $fid;
            $bigId   = $uid;
            $type    = 2;
        } else {
            $smallId = $uid;
            $bigId   = $fid;
            $type    = 1;
        }
        $model = Db::name('Follow');
        $map   = [
            'uid' => $smallId,
            'fid' => $bigId,
        ];
        $relation = $model->where($map)->find();
        if (!$relation) {
            self::$error = '您未关注过此用户';
            return false;
        } else {
            // 如果有关注关系
            if ($relation['type'] == 3) {
                return $model->where($map)->setField('type', (3 - $type));
            } elseif ($relation['type'] == $type) {
                return $model->where($map)->delete();
            } else {
                self::$error = '您未关注过此用户';
                return false;
            }
        }
    }

    /**
     * 获取我的好友关系列表
     * @param  integer $uid  [description]
     * @param  integer $type 1 关注列表，2 被关注 3 好友
     */
    public static function lists($uid, $type = 1, $page = 1, $rows = 10)
    {
        $model = Db::name('Follow');

        switch ($type) {
            case 1:
                $result = $model->where('type IN (1,3) AND uid=' . $uid)->whereOr('type IN (2,3) AND fid=' . $uid)->page($page, $rows)->select();
                break;
            case 2:
                $result = $model->where('type IN (2,3) AND uid=' . $uid)->whereOr('type IN (1,3) AND fid=' . $uid)->page($page, $rows)->select();
                break;
            case 3:
                $result = $model->where('type=3 AND uid=' . $uid)->whereOr('type=3 AND fid=' . $uid)->page($page, $rows)->select();
                break;
        }

        $ids = [];
        foreach ($result as $key => $value) {
            if ($value['uid'] == $uid) {
                array_push($ids, $value['fid']);
            } else {
                array_push($ids, $value['uid']);
            }
        }
        return $ids;
    }
    /**
     * 获取我的好友关系列表
     * @param  integer $uid  [description]
     * @param  integer $type 1 关注数量，2 被关注数量 3 好友数量
     */
    public static function total($uid, $type = 1)
    {
        $model = Db::name('Follow');

        switch ($type) {
            case 1:
                $result = $model->where('type IN (1,3) AND uid=' . $uid)->whereOr('type IN (2,3) AND fid=' . $uid)->count();
                break;
            case 2:
                $result = $model->where('type IN (2,3) AND uid=' . $uid)->whereOr('type IN (1,3) AND fid=' . $uid)->count();
                break;
            case 3:
                $result = $model->where('type=3 AND uid=' . $uid)->whereOr('type=3 AND fid=' . $uid)->count();
                break;
        }

        return $result;
    }
}
