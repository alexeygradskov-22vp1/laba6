<?php

class UserRepository
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function getUsers(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }

    public function getUserById($id): ?array
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user["id"] === $id)
                return $user;
        }

        return null;
    }

    public function getUserByUsername($username): ?array
    {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user["username"] === $username)
                return $user;
        }

        return null;
    }

    public function postUser($username, $password, $name, $email, $gender): int
    {
        $users = $this->getUsers();
        if (empty($users)) $id = 0;
        else $id = $users[array_key_last($users)]["id"] + 1;
        $users[] = [
            "id" => $id,
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "name" => $name,
            "email" => $email,
            "gender" => $gender
        ];
        $users = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $file = fopen($this->path, "w");
        fwrite($file, $users);
        fclose($file);

        return $id;
    }
}