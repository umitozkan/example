<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Register user
    public function register($data)
    {
        // TODO 1- Kullanıcı Kayıt metodunu gerçekleştiriniz. (Her kullanıcı aynı zamanda bir müşteridir)
        $this->db->query("INSERT INTO users (name,email, password) VALUES (:name, :email, :password)");
        $this->db->bind(":name", $data['name']);
        $this->db->bind(":email", $data['email']);
        $this->db->bind(":password", $data['password']);

        if ($this->db->execute()) {
            $userId = $this->db->getLastInsertId();
            $this->db->query("INSERT INTO customers (user_id) VALUES (:user_id)");
            $this->db->bind(":user_id", $userId);
            $this->db->execute();
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password)
    {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind(":email", $email);
        $row = $this->db->single();
        $hashed_password = $row->password;// password_verify php built-in func
        if (password_verify($password, $hashed_password)) {
            return $row;
        } else {
            return false;
        }
    }

    // Find user by email
    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        // Bind value
        $this->db->bind(":email", $email);
        $row = $this->db->single();

        // Check row
        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Get User By ID
    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM users WHERE id = :id');
        // Bind value
        $this->db->bind(":id", $id);
        $row = $this->db->single();

        return $row;
    }
}

?>