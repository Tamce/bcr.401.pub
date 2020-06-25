<?php
namespace App\Modules;

class Session
{
    public function start()
    {
        session_start();
    }

    public function id($new_id = null)
    {
        if (is_null($new_id))
            return session_id();
        session_id($new_id);
        return $this;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function destroy()
    {
        return session_destroy();
    }

    public function delete($key)
    {
        unset($_SESSION[$key]);
    }
}