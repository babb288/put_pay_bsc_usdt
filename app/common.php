<?php
// 应用公共文件

// 这是系统自动生成的公共文件
function aesEncrypt($data): string
{
    $iv = openssl_random_pseudo_bytes(16);
    return openssl_encrypt($data, 'AES-128-CBC', 'c99a11a53a3748269e3f86d7ac38df11', 0, $iv) . "::" . bin2hex($iv);
}

function aesDecrypt($data)
{
    list($encryptedData, $iv) = explode("::", $data);
    return openssl_decrypt($encryptedData, 'AES-128-CBC', 'c99a11a53a3748269e3f86d7ac38df11', 0, hex2bin($iv));
}
