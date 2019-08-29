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
class Auth
{

    /**
     * 返回用户所在组的Id集合
     * @return array
     */
    public static function getGroupIds($uid)
    {
        return Db::name('AuthUser')->where('uid', $uid)->column('auth_id');
    }

    /**
     * 检查菜单权限
     * @return boolean
     */
    public static function checkAuth($uid, $node)
    {
        $adminUsers = \think\Config::get('administrator');
        // 当前用户不是超级用户,要做权限验证
        if (!in_array($uid, $adminUsers)) {
            // 根据控制器 去验证
            $nodeId = Db::name('Menu')->where('url', $node)->value('id');
            if ($nodeId) {
                $nodes = session('user_auth_nodes');
                if (!$nodes) {
                    // 获取当前用户的授权节点
                    $gIds = self::getGroupIds($uid);
                    if ($gIds) {
                        $nodes = Db::name('Auth')->where('id', 'in', $gIds)->column('rules');
                        $nodes = implode($nodes, ',');
                        $nodes = trim($nodes, ',');
                        $nodes = explode(',', $nodes);
                        session('user_auth_nodes', $nodes);
                    } else {
                        return false;
                    }
                }
                if (in_array($nodeId, $nodes)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                // 不存在的节点 不验证
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * 获取用户菜单节点Id集合
     * @param  [type] $uid 用户UID
     * @return array
     */
    public static function getMenuIds($uid)
    {
        $gIds    = self::getGroupIds($uid);
        $menuIds = '';
        if ($gIds) {
            $menuIds = Db::name('Auth')->where('id', 'in', $gIds)->column('rules');
            $menuIds = implode($menuIds, ',');
            $menuIds = trim($menuIds, ',');
        }

        $openMap = [
            'status' => 2,
            'auth'   => 0,
        ];
        $openMenus = Db::name('Menu')->where($openMap)->column('id');
        $openMenus = implode($openMenus, ',');

        if (!empty($openMenus)) {
            $menuIds .= ',' . $openMenus;
        }
        return trim($menuIds, ',');
    }
}
