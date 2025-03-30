<?php
namespace \core\client\auth;

class http {

    private $username;
    private $password;
    private $hashedPassword;

    public function __construct($username, $hashedPassword) {
        $this->username = $username;
        $this->hashedPassword = $hashedPassword;
    }

    public function authenticate() {
        // Проверяем, был ли отправлен запрос на аутентификацию
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];

            // Проверяем, совпадает ли логин
            if ($username === $this->username) {
                // Проверяем пароль с использованием функции password_verify
                if (password_verify($password, $this->hashedPassword)) {
                    // Аутентификация успешна
                    echo "Привет, $username! Вы успешно вошли.";
                    return true;
                }
            }

            // Аутентификация не удалась
            $this->sendAuthChallenge();
            echo "Ошибка аутентификации.";
            exit;
        } else {
            // Запрос аутентификации еще не был отправлен
            $this->sendAuthChallenge();
            echo "Вам необходимо ввести логин и пароль.";
            exit;
        }
    }

    private function sendAuthChallenge() {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
    }
}

?>
