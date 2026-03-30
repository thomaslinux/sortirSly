<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
class JWTService
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }
    public function getSecret(): string
    {
        return $this->secret;
    }
    public function generate(array $header, array $payload, string $key): string
    {
        return JWT::encode($payload, $key, $header['alg']);
    }

    public function getPayload(string $token): array
    {
        $key = new Key($this->getSecret(), 'HS256');
        $decoded = JWT::decode($token, $key);
        return (array) $decoded;
    }

    public function isValid(string $token): bool
    {
        try {
            $key = new Key($this->getSecret(), 'HS256');
            JWT::decode($token, $key);
            return true;
        }
        catch (\Exception) {
            return false;
        }
    }
    public function isExpired(string $token):bool
    {
        try {
            $key = new Key($this->getSecret(), 'HS256');
            $decoded = JWT::decode($token, $key);
            return isset($decoded->exp) && $decoded->exp < time();
        }
        catch (\Exception){
            return true;
        }
    }
    public function check(string $token, string $key): bool
    {
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return true;
        }
        catch (\Exception){
            return false;
        }
    }
}
