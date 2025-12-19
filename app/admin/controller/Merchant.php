<?php

namespace app\admin\controller;

use app\admin\model\Merchant as MerchantModel;
use app\Request;

class Merchant
{
    public function __construct(
        private MerchantModel $merchant,
        private Request $request
    ) {}

    /**
     * 商户列表页面
     */
    public function index(): \think\response\View
    {
        return view('merchant/index');
    }

    /**
     * 获取商户列表数据
     */
    public function list(): \think\response\Json
    {
        $params = $this->request->param();
        
        $page = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 15;
        
        // 处理搜索参数（支持 searchParams JSON 字符串格式）
        $searchParams = [];
        if (isset($params['searchParams']) && !empty($params['searchParams'])) {
            $searchParams = json_decode($params['searchParams'], true) ?: [];
        }
        
        // 兼容直接传参的方式
        $username = $searchParams['username'] ?? $params['username'] ?? '';
        $uid = $searchParams['uid'] ?? $params['uid'] ?? '';
        $status = $searchParams['status'] ?? $params['status'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($uid) {
            $where[] = ['uid', '=', $uid];
        }
        if ($status !== '') {
            $where[] = ['status', '=', $status];
        }

        $list = $this->merchant->where($where)
            ->order('id', 'desc')
            ->hidden(['key','ip'])
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
            ]);

        return json([
            'code' => 0,
            'msg' => 'success',
            'count' => $list->total(),
            'data' => $list->items()
        ]);
    }
    

    /**
     * 显示添加商户表单
     */
    public function addForm(): \think\response\View
    {
        return view('merchant/add');
    }

    /**
     * 显示编辑商户表单
     */
    public function editForm(): \think\response\View
    {
        $id = $this->request->param('id', 0);
        $merchant = $this->merchant->hidden(['key'])->find($id);
        
        if (!$merchant) {
            return view('merchant/error', ['msg' => '商户不存在']);
        }

        // 处理IP白名单
        $ipWhitelistArray = [];
        if ($merchant->ip_whitelist) {
            $ipWhitelistArray = json_decode($merchant->ip_whitelist, true) ?: [];
            $ipWhitelistArray=[];
        }

        // 转换为换行分隔的字符串用于显示
        $merchant->ip_whitelist_text = implode("\n", $ipWhitelistArray);
        $merchant->key = '';
        return view('merchant/edit', ['merchant' => $merchant]);
    }

    /**
     * 生成唯一的6位商户UID
     */
    private function generateUid(): string
    {
        $maxAttempts = 100; // 最大尝试次数
        $attempts = 0;
        
        do {
            // 生成6位随机数字（100000-999999）
            $uid = str_pad((string)mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $exists = $this->merchant->where('uid', $uid)->find();
            $attempts++;
            
            if ($attempts >= $maxAttempts) {
                throw new \RuntimeException('无法生成唯一的商户UID，请稍后重试');
            }
        } while ($exists);
        
        return $uid;
    }

    /**
     * 生成商户密钥（用户名 + 随机盐 + 随机值）
     */
    private function generateKey(string $username): string
    {
        // 生成随机盐（32位随机字符串）
        $salt = bin2hex(random_bytes(16));
        
        // 生成随机值（32位随机字符串）
        $random = bin2hex(random_bytes(16));
        
        // 组合：用户名 + 随机盐 + 随机值
        $key = $username . '_' . $salt . '_' . $random;
        
        // 可选：对key进行哈希处理（如果需要更安全的存储）
        // $key = hash('sha256', $key);
        
        return $key;
    }

    /**
     * 添加商户
     */
    public function add(): \think\response\Json
    {
        $params = $this->request->param();

        // 自动生成商户UID
        $params['uid'] = $this->generateUid();

        // 检查用户名是否已存在
        if ($this->merchant->where('username', $params['username'])->find()) {
            return json(['code' => -1, 'msg' => '用户名已存在']);
        }

        // 自动生成商户密钥（用户名 + 随机盐 + 随机值）
        $params['key'] = $this->generateKey($params['username']);

        // 使用MD5加密密码
        $params['password'] = md5($params['password']);

        // 处理IP白名单
        if (isset($params['ip_whitelist']) && is_array($params['ip_whitelist'])) {
            $params['ip_whitelist'] = json_encode($params['ip_whitelist']);
        }

        // 添加商户
        $result = $this->merchant->save($params);

        if ($result) {
            return json(['code' => 1, 'msg' => '添加成功']);
        } else {
            return json(['code' => -1, 'msg' => '添加失败']);
        }
    }

    /**
     * 编辑商户
     */
    public function edit(): \think\response\Json
    {
        $params = $this->request->param();
        $id = $params['id'] ?? 0;

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $merchant = $this->merchant->find($id);
        if (!$merchant) {
            return json(['code' => -1, 'msg' => '商户不存在']);
        }

        // UID不允许修改，移除参数中的uid
        unset($params['uid']);
        
        // address不允许修改，移除参数中的address
        unset($params['address']);

        unset($params['username']);

        unset($params['contract_address']);

        unset($params['balance']);

        unset($params['fee_rate']);
        unset($params['key']);
        //线上环境移除白名单
//        unset($params['ip_whitelist']);

//        if (isset($params['username']) && $params['username'] != $merchant->username) {
//            if ($this->merchant->where('username', $params['username'])->find()) {
//                return json(['code' => -1, 'msg' => '用户名已存在']);
//            }
//        }

        // 处理IP白名单
        if (isset($params['ip_whitelist']) && is_array($params['ip_whitelist'])) {
            $params['ip_whitelist'] = json_encode($params['ip_whitelist']);
        }

        // 更新商户
        $result = $merchant->save($params);

        if ($result) {
            return json(['code' => 1, 'msg' => '更新成功']);
        } else {
            return json(['code' => -1, 'msg' => '更新失败']);
        }
    }

    /**
     * 删除商户
     */
    public function delete(): \think\response\Json
    {
        $id = $this->request->param('id', 0);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $merchant = $this->merchant->find($id);
        if (!$merchant) {
            return json(['code' => -1, 'msg' => '商户不存在']);
        }

        $result = $merchant->delete();

        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => -1, 'msg' => '删除失败']);
        }
    }

    /**
     * 更新密码
     */
    public function updatePassword(): \think\response\Json
    {
        $params = $this->request->param();
        $id = $params['id'] ?? 0;

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $merchant = $this->merchant->find($id);
        if (!$merchant) {
            return json(['code' => -1, 'msg' => '商户不存在']);
        }

        // 使用MD5加密密码
        $merchant->password = md5($params['password']);
        $result = $merchant->save();

        if ($result) {
            return json(['code' => 1, 'msg' => '密码更新成功']);
        } else {
            return json(['code' => -1, 'msg' => '密码更新失败']);
        }
    }

    /**
     * 更新状态
     */
    public function updateStatus(): \think\response\Json
    {
        $id = $this->request->param('id', 0);
        $status = $this->request->param('status', -1);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        if (!in_array($status, [0, 1])) {
            return json(['code' => -1, 'msg' => '状态值不正确']);
        }

        $merchant = $this->merchant->find($id);
        if (!$merchant) {
            return json(['code' => -1, 'msg' => '商户不存在']);
        }

        $merchant->status = $status;
        $result = $merchant->save();

        if ($result) {
            return json(['code' => 1, 'msg' => '状态更新成功']);
        } else {
            return json(['code' => -1, 'msg' => '状态更新失败']);
        }
    }
}

