<?php

namespace App\core;

class Session {
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function post($key)
    {
        return $_SESSION[$key] ?? null;
    }

    public static function destroy()
    {
        session_destroy();
    }

    public static function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    public static function getFlash($key)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (isset($_SESSION['flash'][$key])) {
            $message = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]); 
            return $message;
        }
        return null;
    }


    public static function getOrCreateGuestIdentifier() {
        self::start();
        
        if (!isset($_SESSION['guest_identifier'])) {
            $_SESSION['guest_identifier'] = self::generateUniqueIdentifier();
        }
        
        return $_SESSION['guest_identifier'];
    }

    private static function generateUniqueIdentifier() {
        return bin2hex(random_bytes(16)); // 32 caractères hexadécimaux
    }


    public static function associateCartAfterLogin($userId) {
        self::start();
        
        if (isset($_SESSION['guest_identifier'])) {
            $guestIdentifier = $_SESSION['guest_identifier'];
            
            // Créer une instance du repository de panier
            $cartRepo = new \App\Repositories\CartRepository();
            
            // Récupérer les IDs des paniers
            $guestCartId = $cartRepo->getCartIdBySessionId($guestIdentifier);
            $userCartId = $cartRepo->getCartIdByUserId($userId);
            
            if ($guestCartId && $userCartId) {
                // Fusionner les paniers
                $cartRepo->mergeCarts($userCartId, $guestCartId);
            } elseif ($guestCartId) {
                // Associer le panier du visiteur à l'utilisateur
                $cartRepo->assignCartToUser($guestCartId, $userId);
            }
            
            // Supprimer l'identifiant de visiteur
            unset($_SESSION['guest_identifier']);
        }
    }

}