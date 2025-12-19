<?php

namespace app\admin\controller;

use app\admin\model\CollectTask;
use app\admin\model\Wallet as WalletModel;
use app\admin\model\Merchant as MerchantModel;
use app\admin\model\SendDetail;
use app\Bsc;
use app\Request;
use app\admin\model\Address;
use think\facade\Db;

class Wallet
{
    public function __construct(
        private WalletModel $wallet,
        private Request $request,
        private Bsc $bsc,
        private MerchantModel $merchant,
        private Address $address,
        private CollectTask $collectTask,
        private SendDetail $sendDetail,
    ) {}

    /**
     * 钱包列表页面
     */
    public function index(): \think\response\View
    {
        return view('wallet/index');
    }

    /**
     * 获取钱包列表数据
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
        $address = $searchParams['address'] ?? $params['address'] ?? '';
        $type = $searchParams['type'] ?? $params['type'] ?? '';

        $where = [];
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        if ($address) {
            $where[] = ['address', 'like', '%' . $address . '%'];
        }
        if ($type !== '') {
            $where[] = ['type', '=', $type];
        }

        $list = $this->wallet->where($where)
            ->order('id', 'desc')
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
     * 刷新钱包
     */
    public function refresh(): \think\response\Json
    {
        $id = $this->request->param('id', 0);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        $wallet = $this->wallet->find($id);
        if (!$wallet) {
            return json(['code' => -1, 'msg' => '钱包不存在']);
        }

        $balance = $this->bsc->getTokenBalance(tokenContract: '0x55d398326f99059ff775485246999027b3197955',address: $wallet->address);

        $bnb_balance= $this->bsc->getBnbBalance(address: $wallet->address);

        $wallet->save([
            'usdt_balance'       => $balance,
            'bnb_balance'   => $bnb_balance,
        ]);

        return json(['code' => 1, 'msg' => '刷新成功']);
    }


    /**
     * 一键归集页面
     */
    public function collect(): \think\response\View
    {
        return view('wallet/collect');
    }

    /**
     * 获取商户列表（用于下拉选择）
     */
    public function getMerchantList(): \think\response\Json
    {
        $merchants = $this->merchant->where('status', 1)
            ->field('id,username')
            ->order('id', 'desc')
            ->select();
        
        return json([
            'code' => 1,
            'msg' => 'success',
            'data' => $merchants
        ]);
    }

    /**
     * 执行一键归集
     */
    public function doCollect(): \think\response\Json
    {
        $params = $this->request->param();

        if($this->collectTask->where('username',$params['username'])->where('status',0)->find()){
            return json(array('code' => -1,'msg' => '已存在任务,请等待任务完成'));
        }


        $total_address_result = $this->address
            ->where('username',$params['username'])
            ->where('is_authorized',1)
            ->where('status',1)
            ->where('balance','>',0)
            ->field('address,balance')
            ->select();

        $total_address_amount = $this->address
            ->where('username',$params['username'])
            ->where('is_authorized',1)
            ->where('status',1)
            ->where('balance','>',0)
            ->field('address,balance')
            ->sum('balance');

        $merchant = $this->merchant
            ->where('username',$params['username'])
            ->field('fee_rate,balance,contract_address')
            ->find();


        $contract_fee = count($total_address_result);

        if($contract_fee == 0){
            return json(array('code' => -1,'msg' => '暂无可归集地址'));
        }

        if($merchant->balance < $contract_fee){
            return json(array('code' => -1,'msg' => '预存笔数不足'));
        }


        $addresses = array_column($total_address_result->toArray(), 'address');
        $amounts   = array_column($total_address_result->toArray(), 'balance');

        $put_wallet = $this->wallet
            ->where('username',$params['username'])
            ->where('type','put')
            ->find();

        $this->bsc->setAddress($put_wallet->address);
        $this->bsc->setPrivateKey(aesDecrypt($put_wallet->key));

        $data = $this->bsc->estimateBatchTransferFee(
            contractAddress: $merchant->contract_address,
            tokenContract: '0x55d398326f99059fF775485246999027B3197955',
            froms: $addresses,
            to: $put_wallet->address,
            amounts: $amounts,
        );

        if($data == null){
            return json(array('code' => -1,'msg' => '预估gas费用失败,请稍后在试'));
        }

        if($put_wallet->bnb_balance < $data['feeBnb']){
            return json(array('code' => -1,'msg' => 'bnb不足'));
        }

        $hash = $this->bsc->batchTransferFrom(
            contractAddress: $merchant->contract_address,
            tokenContract: '0x55d398326f99059fF775485246999027B3197955',
            froms: $addresses,
            to: $put_wallet->address,
            amounts: $amounts,
            gasLimit: $data['gas']
        );

        if($hash == null){
            return json(array('code' => -1,'msg' => '转账失败,请联系技术检查'));
        }

        $merchant->save([
            'balance' => Db::raw('balance-'.$contract_fee)
        ]);

        $this->collectTask->create([
            'username'          =>  $params['username'],
            'wallet_count'      =>  count($total_address_result),
            'collect_amount'    =>  $total_address_amount,
            'contract_fee'      =>  $contract_fee,
            'platform_fee'      =>  $total_address_amount * $merchant->fee_rate,
            'collect_hash'      =>  $hash
        ]);

        return json(['code' => 1, 'msg' => '归集成功', 'data' => $total_address_result]);
    }

    /**
     * 代收钱包一键下发
     */
    public function oneKeySend(): \think\response\Json
    {
        $id = (int)$this->request->param('id', 0);

        if (!$id) {
            return json(['code' => -1, 'msg' => '参数错误']);
        }

        // 获取钱包信息
        $wallet = $this->wallet->find($id);
        if (!$wallet) {
            return json(['code' => -1, 'msg' => '钱包不存在']);
        }

        // 仅允许代收钱包下发
        if ($wallet->type !== 'put') {
            return json(['code' => -1, 'msg' => '只有代收钱包可以进行下发操作']);
        }

        // 查询商户信息（按用户名关联）
        $merchant = $this->merchant->where('username', $wallet->username)->find();
        if (!$merchant) {
            return json(['code' => -1, 'msg' => '商户不存在']);
        }

        if (empty($merchant->address)) {
            return json(['code' => -1, 'msg' => '商户未配置下发地址']);
        }

        // 校验USDT余额
        $usdtBalance = (string)$wallet->usdt_balance;
        if (bccomp($usdtBalance, '0', 8) <= 0) {
            return json(['code' => -1, 'msg' => 'USDT余额为0，无需下发']);
        }

        // 校验BNB手续费是否充足（简单判断：至少需要 0.00005 BNB）
        $bnbBalance = $this->bsc->getBnbBalance($wallet->address);
        if ($bnbBalance === null || bccomp($bnbBalance, '0.00001', 8) < 0) {
            return json(['code' => -1, 'msg' => 'BNB余额不足，无法支付网络手续费']);
        }

        // 配置BSC钱包
        $this->bsc->setAddress($wallet->address);
        $this->bsc->setPrivateKey(aesDecrypt($wallet->key));
        
        // 执行USDT下发（全部余额）
        $tokenContract = '0x55d398326f99059fF775485246999027B3197955';

        $usdtBalance= $usdtBalance -0.00001;

        $gas = $this->bsc->callTransferToken(
            token: $tokenContract,
            address: $merchant->address,
            amount:$usdtBalance
        );

        if($gas == null ){
            return json(array('code' => 1,'msg' => '操作失败,请检查'));
        }

        $hash = $this->bsc->transferToken(
            token: $tokenContract,
            address: $merchant->address,
            amount:$usdtBalance
        );

        if ($hash === null) {
            return json(['code' => -1, 'msg' => '下发失败，请检查USDT/BNB余额或稍后重试']);
        }

        // 写入下发明细表
        $this->sendDetail->create([
            'username'       => $wallet->username,
            'wallet_id'      => $wallet->id,
            'wallet_address' => $wallet->address,
            'to_address'     => $merchant->address,
            'token_symbol'   => 'USDT',
            'amount'         => $usdtBalance,
            'txid'           => $hash,
            'status'         => 1,
            'remark'         => '一键下发成功',
        ]);

        // 下发成功后，刷新一次钱包余额
        $newUsdt = $this->bsc->getTokenBalance(tokenContract: $tokenContract, address: $wallet->address);
        $newBnb  = $this->bsc->getBnbBalance(address: $wallet->address);

        $wallet->save([
            'usdt_balance' => $newUsdt,
            'bnb_balance'  => $newBnb,
        ]);

        return json([
            'code' => 1,
            'msg'  => '下发成功',
            'data' => [
                'txid'           => $hash,
                'from_address'   => $wallet->address,
                'to_address'     => $merchant->address,
                'amount_usdt'    => $usdtBalance,
                'new_usdt'       => $newUsdt,
                'new_bnb'        => $newBnb,
            ]
        ]);
    }



}

