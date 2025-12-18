<?php



namespace app\admin\service;

use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Validation\Validator;

class JwtService
{


    private $configuration;
    protected $expire;

    public function __construct()
    {

        $this->configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(config('jwt.key'))
        );
        $this->expire = config('jwt.expire');
    }


    public function generateToken($request,$data): string
    {

        $builder = $this->configuration->builder()
            ->issuedBy($request->host()) // 设置签发者
            ->permittedFor($request->host()) // 设置接收者
            ->identifiedBy('4f1g23a12aa', true) // 设置令牌ID
            ->issuedAt(new DateTimeImmutable()) // 设置签发时间
            ->expiresAt(new DateTimeImmutable('+'.$this->expire.' seconds')) // 设置过期时间
            ->withClaim('data', $data); // 设置自定义数据

        $token = $builder->getToken($this->configuration->signer(), $this->configuration->signingKey()); // 获取JWT令牌对象

        return (string) $token->toString();
    }

    public function verifyToken($request,string $token)
    {

        $token = $this->configuration->parser()->parse($token);

        if(!(new Validator)->validate($token,
            new SignedWith($this->configuration->signer(), $this->configuration->verificationKey()),
            new ValidAt(SystemClock::fromSystemTimezone()),
            new IssuedBy($request->host()),
            new PermittedFor($request->host()),
            new IdentifiedBy('4f1g23a12aa'))){

            return false;
        }
        return $token->claims()->get('data');

    }
}
