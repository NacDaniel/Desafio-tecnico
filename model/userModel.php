<?php

class userModel
{
    private static $instance = null; // instância da classe
    private $con = null; // conexão do BD

    public static function getInstance(): userModel
    {
        if (self::$instance == null) {
            self::$instance = new userModel();
            self::$instance->initDatabase();
        }

        return self::$instance;
    }

    private function initDatabase()
    {
        try {
            $this->con = mysqli_init();
            $this->con->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);
            if (!$this->con->real_connect("localhost", "root", "admin", 'publico')) {
                $this->con = null;
                throw new Exception("Falha ao conectar no banco de dados.");
            }
            //if (!$this->con->query("CREATE TABLE IF NOT EXISTS users(id INT(5) AUTO_INCREMENT, fullname TEXT(200), PRIMARY KEY (id))")) {
            //    throw new Exception("Falha ao criar a tabela de números");
            //  }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getUser($id = null): array|bool
    {
        if (!$this->con) {
            throw new Exception("Conexão com o banco de dados não está aberta! Tente novamente.");
        }

        $query = "SELECT * FROM users";
        if ($id) {
            $query .= " WHERE id = " . $id;
        }
        $query = $this->con->query($query);
        if (!$query) {
            throw new Exception("Falha ao obter usuários.");
        }

        return $this->iterateUsersDatabase($query);
    }

    private function iterateUsersDatabase($queryResult)
    {
        $data = [];
        while ($row = mysqli_fetch_array($queryResult, MYSQLI_ASSOC)) {
            array_unshift($data, $row);
        }
        return $data;
    }

    public function insertUser($data = []): bool|int
    {
        return true;
    }

    public function updateUser($id, $data = []): bool
    {
        return true;
    }


    public function deleteUser($id): bool
    {
        $this->con->query("DELETE FROM users WHERE id = $id");

        if ($this->con->affected_rows < 1) {
            throw new Exception("Falha ao deletar o usuário");
        }
        
        return true;
    }

    public function deleteInstance()
    {
        // fechar conexão do BD antes
        if ($this->con) {
            $this->con->close();
            $this->con = null;
        }
        self::$instance = null;
    }

    public function __destruct()
    {
        if ($this->con) {
            $this->con->close();
        }
    }
}

?>