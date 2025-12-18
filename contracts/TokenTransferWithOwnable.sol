// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

/**
 * @title TokenTransferWithOwnable
 * @dev 带权限管理的代币转账合约（包含Owner功能）
 */
contract TokenTransferWithOwnable {
    
    address public owner;
    
    // 修饰符：仅所有者
    modifier onlyOwner() {
        require(msg.sender == owner, "TokenTransfer: caller is not the owner");
        _;
    }
    
    // 接收BNB
    receive() external payable {
        // 允许合约接收BNB
    }
    
    // Fallback函数
    fallback() external payable {
        // 允许合约接收BNB
    }
    
    constructor() {
        owner = msg.sender;
    }
    
    /**
     * @dev 转移所有权
     * @param newOwner 新所有者地址
     */
    function transferOwnership(address newOwner) external onlyOwner {
        require(newOwner != address(0), "TokenTransfer: new owner is the zero address");
        owner = newOwner;
    }
    
    /**
     * @dev 执行单笔transferFrom转账到合约地址（仅所有者）
     * @param token ERC20代币合约地址
     * @param from 转出地址（必须已授权给本合约）
     * @param amount 转账数量
     * @return success 是否成功
     */
    function transferFromToContract(
        address token,
        address from,
        uint256 amount
    ) external onlyOwner returns (bool success) {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(from != address(0), "TokenTransfer: invalid from address");
        require(amount > 0, "TokenTransfer: amount must be greater than 0");
        
        // 接收地址固定为当前合约地址
        address to = address(this);
        
        // 检查授权额度
        uint256 allowance = IERC20(token).allowance(from, address(this));
        require(allowance >= amount, "TokenTransfer: insufficient allowance");
        
        // 执行transferFrom
        bool result = IERC20(token).transferFrom(from, to, amount);
        require(result, "TokenTransfer: transferFrom failed");
        
        return true;
    }
    
    /**
     * @dev 执行单笔transferFrom转账（可指定接收地址）（仅所有者）
     * @param token ERC20代币合约地址
     * @param from 转出地址（必须已授权给本合约）
     * @param to 接收地址
     * @param amount 转账数量
     * @return success 是否成功
     */
    function transferFrom(
        address token,
        address from,
        address to,
        uint256 amount
    ) external onlyOwner returns (bool success) {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(from != address(0), "TokenTransfer: invalid from address");
        require(to != address(0), "TokenTransfer: invalid to address");
        require(amount > 0, "TokenTransfer: amount must be greater than 0");
        
        // 检查授权额度
        uint256 allowance = IERC20(token).allowance(from, address(this));
        require(allowance >= amount, "TokenTransfer: insufficient allowance");
        
        // 执行transferFrom
        bool result = IERC20(token).transferFrom(from, to, amount);
        require(result, "TokenTransfer: transferFrom failed");
        
        return true;
    }
    
    /**
     * @dev 批量执行transferFrom转账到合约地址（仅所有者）
     * @param token ERC20代币合约地址
     * @param froms 转出地址数组
     * @param amounts 转账数量数组
     * @return success 是否全部成功
     */
    function batchTransferFromToContract(
        address token,
        address[] calldata froms,
        uint256[] calldata amounts
    ) external onlyOwner returns (bool success) {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(froms.length == amounts.length, "TokenTransfer: array length mismatch");
        require(froms.length > 0, "TokenTransfer: empty arrays");
        
        // 接收地址固定为当前合约地址
        address to = address(this);
        uint256 totalTransfers = froms.length;
        
        for (uint256 i = 0; i < totalTransfers; i++) {
            require(froms[i] != address(0), "TokenTransfer: invalid from address");
            require(amounts[i] > 0, "TokenTransfer: amount must be greater than 0");
            
            // 检查授权额度
            uint256 allowance = IERC20(token).allowance(froms[i], address(this));
            require(allowance >= amounts[i], "TokenTransfer: insufficient allowance");
            
            // 执行transferFrom
            bool result = IERC20(token).transferFrom(froms[i], to, amounts[i]);
            require(result, "TokenTransfer: transferFrom failed");
        }
        
        return true;
    }
    
    /**
     * @dev 批量执行transferFrom转账（可指定接收地址）（仅所有者）
     * @param token ERC20代币合约地址
     * @param froms 转出地址数组
     * @param to 接收地址（所有转账都转到此地址）
     * @param amounts 转账数量数组
     * @return success 是否全部成功
     */
    function batchTransferFrom(
        address token,
        address[] calldata froms,
        address to,
        uint256[] calldata amounts
    ) external onlyOwner returns (bool success) {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(to != address(0), "TokenTransfer: invalid to address");
        require(froms.length == amounts.length, "TokenTransfer: array length mismatch");
        require(froms.length > 0, "TokenTransfer: empty arrays");
        
        uint256 totalTransfers = froms.length;
        
        for (uint256 i = 0; i < totalTransfers; i++) {
            require(froms[i] != address(0), "TokenTransfer: invalid from address");
            require(amounts[i] > 0, "TokenTransfer: amount must be greater than 0");
            
            // 检查授权额度
            uint256 allowance = IERC20(token).allowance(froms[i], address(this));
            require(allowance >= amounts[i], "TokenTransfer: insufficient allowance");
            
            // 执行transferFrom
            bool result = IERC20(token).transferFrom(froms[i], to, amounts[i]);
            require(result, "TokenTransfer: transferFrom failed");
        }
        
        return true;
    }
    
    /**
     * @dev 提取代币（仅所有者，扣除手续费）
     * @param token ERC20代币合约地址
     * @param to 接收地址
     * @param amount 提取数量（0表示提取全部）
     * @param fee 手续费金额
     * @param feeRecipient 手续费接收地址
     */
    function withdrawToken(
        address token,
        address to,
        uint256 amount,
        uint256 fee,
        address feeRecipient
    ) external onlyOwner {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(to != address(0), "TokenTransfer: invalid to address");
        
        IERC20 tokenContract = IERC20(token);
        uint256 balance = tokenContract.balanceOf(address(this));
        require(balance > 0, "TokenTransfer: no token balance");
        
        // 确定提取金额
        uint256 withdrawAmount = amount == 0 ? balance : amount;
        require(withdrawAmount <= balance, "TokenTransfer: insufficient balance");
        require(fee <= withdrawAmount, "TokenTransfer: fee cannot exceed withdraw amount");
        
        // 计算接收方实际到账金额
        uint256 recipientAmount = withdrawAmount - fee;
        
        // 转账手续费（如果有）
        if (fee > 0) {
            require(feeRecipient != address(0), "TokenTransfer: invalid fee recipient address");
            require(tokenContract.transfer(feeRecipient, fee), "TokenTransfer: fee transfer failed");
        }
        
        // 转账剩余金额给接收方
        require(tokenContract.transfer(to, recipientAmount), "TokenTransfer: token withdraw failed");
    }

    
    
    /**
     * @dev 提取BNB（仅所有者）
     * @param to 接收地址
     * @param amount 提取数量（0表示提取全部）
     */
    function withdrawBnb(
        address to,
        uint256 amount
    ) external onlyOwner {
        require(to != address(0), "TokenTransfer: invalid to address");
        
        uint256 balance = address(this).balance;
        require(balance > 0, "TokenTransfer: no BNB balance");
        
        // 如果amount为0，提取全部余额
        uint256 withdrawAmount = amount == 0 ? balance : amount;
        require(withdrawAmount <= balance, "TokenTransfer: insufficient balance");
        
        // 使用call方式转账BNB（推荐的安全方式）
        (bool success, ) = payable(to).call{value: withdrawAmount}("");
        require(success, "TokenTransfer: BNB withdraw failed");
    }
    
    /**
     * @dev 查询合约代币余额
     * @param token ERC20代币合约地址
     * @return balance 合约的代币余额
     */
    function getContractTokenBalance(address token) external view returns (uint256 balance) {
        require(token != address(0), "TokenTransfer: invalid token address");
        return IERC20(token).balanceOf(address(this));
    }
    
    /**
     * @dev 查询合约BNB余额
     * @return balance 合约的BNB余额
     */
    function getContractBnbBalance() external view returns (uint256 balance) {
        return address(this).balance;
    }
    
    /**
     * @dev 检查授权额度
     * @param token ERC20代币合约地址
     * @param tokenOwner 代币拥有者地址
     * @param spender 被授权者地址
     * @return allowance 授权额度
     */
    function checkAllowance(
        address token,
        address tokenOwner,
        address spender
    ) external view returns (uint256 allowance) {
        require(token != address(0), "TokenTransfer: invalid token address");
        require(tokenOwner != address(0), "TokenTransfer: invalid token owner address");
        require(spender != address(0), "TokenTransfer: invalid spender address");
        return IERC20(token).allowance(tokenOwner, spender);
    }
    
    /**
     * @dev 检查余额
     * @param token ERC20代币合约地址
     * @param account 账户地址
     * @return balance 账户余额
     */
    function checkBalance(
        address token,
        address account
    ) external view returns (uint256 balance) {
        require(token != address(0), "TokenTransfer: invalid token address");
        return IERC20(token).balanceOf(account);
    }
}

/**
 * @title IERC20
 * @dev ERC20代币标准接口
 */
interface IERC20 {
    function totalSupply() external view returns (uint256);
    function balanceOf(address account) external view returns (uint256);
    function transfer(address to, uint256 amount) external returns (bool);
    function allowance(address owner, address spender) external view returns (uint256);
    function approve(address spender, uint256 amount) external returns (bool);
    function transferFrom(address from, address to, uint256 amount) external returns (bool);
    
    event Transfer(address indexed from, address indexed to, uint256 value);
    event Approval(address indexed owner, address indexed spender, uint256 value);
}
