<?php
namespace App\Service;

use DateTimeImmutable;

class JWTService
{

    public function __construct(private readonly string $secret)
    {}

    /**
     * Génération du JWT
     * @param array $header
     * @param array $payload
     * @param int $validity
     * @return string
     */
    public function generate(array $payload, array $header = ['typ' => 'JWT', 'alg' => 'HS256'], int $validity = 10800): string
    {
        if($validity > 0){
            $now = new DateTimeImmutable();
            $exp = $now->getTimestamp() + $validity;

            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        }

        // On encode en base64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // On "nettoie" les valeurs encodées (retrait des +, / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload);

        // On génère la signature
        $secret = base64_encode($this->secret);
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);

        // On crée le token
        return "$base64Header.$base64Payload.$signature";
    }

    /**
     * On vérifie que le token est valide (correctement formé)
     * @param string $token
     * @return bool
     */
    public function isValid(string $token): bool
    {
        return preg_match(
                '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
                $token
            ) === 1;
    }

    /**
     * On récupère le Payload
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        // On démonte le token
        $array = explode('.', $token);

        // On décode le Payload
        return json_decode(base64_decode($array[1]), true);
    }

    /**
     * On récupère le Header
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        // On démonte le token
        $array = explode('.', $token);

        // On décode le Header
        return json_decode(base64_decode($array[0]), true);
    }

    /**
     * On vérifie si le token a expiré
     * @param string $token
     * @return bool
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);

        $now = new DateTimeImmutable();

        return $payload['exp'] < $now->getTimestamp();
    }

    /**
     * On vérifie la signature du Token
     * @param string $token
     * @return bool
     */
    public function check(string $token): bool
    {
        // On récupère le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        // On régénère un token
        $verifToken = $this->generate($payload, $header);

        return $token === $verifToken;
    }
}