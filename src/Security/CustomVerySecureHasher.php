<?php

namespace App\Security;

use Symfony\Component\PasswordHasher\Exception\InvalidPasswordException;
use Symfony\Component\PasswordHasher\Hasher\CheckPasswordLengthTrait;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CustomVerySecureHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    private $param;

    public function __construct(ParameterBagInterface $params){
        $this->param = $params;
    }

    public function hash(string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException();
        }

        $csv = iconv("UTF-8", "Windows-1252", $this->param->get('app.hash_key'));
        $hashedPassword = strtoupper(hash_hmac('sha256',$plainPassword, $csv));

        return $hashedPassword;
    }

    public function verify(string $hashedPassword, string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }
        
        $csv = iconv("UTF-8", "Windows-1252", $this->param->get('app.hash_key'));
        $hashVerif = strtoupper(hash_hmac('sha256',$plainPassword, $csv));

        if(hash_equals($hashedPassword, $hashVerif)){
            return true;
        }else{
            return false;
        }
    }

    public function needsRehash(string $hashedPassword) :bool{
        return false;
    }

}