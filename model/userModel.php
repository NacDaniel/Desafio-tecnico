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

        $result = false;
        if ($id) {
            $prepare = $this->con->prepare("SELECT * FROM users WHERE id = ?");
            $prepare->bind_param("i", $id);
            $prepare->execute();
            $result = $prepare->get_result();
            $prepare->close();
        } else {
            $result = $this->con->query("SELECT * FROM users");
        }

        if (!$result) {
            throw new Exception("Falha ao obter usuários.");
        }

        return $this->iterateUsersDatabase($result);
    }



    private function iterateUsersDatabase($queryResult)
    {
        $data = [];
        while ($row = $queryResult->fetch_assoc()) {
            array_unshift($data, $row);
        }
        return $data;
    }

    public function insertUser($data = []): bool|int
    {
        $this->checkDataRegistry($data); // lança exception caso apresente algum "problema"

        // deletar depois. Até funciona, mas impede de chamar o método que escapa as strings
        //$query = sprintf("INSERT INTO users(" . implode(",", array_keys($data)) . ") VALUES(\"%s\", \"%s\", \"%s\", \"%s\", \"%s\")", ...array_values($data));

        $prepare = $this->con->prepare("INSERT INTO users(fullname,birthday,bio,address,imageURL) values(?,?,?,?,?)");
        $prepare->bind_param("sssss", $data["fullname"], $data["birthday"], $data["bio"], $data["address"], $data["imageURL"]);
        $prepare->execute();
        $idGenerated = $prepare->insert_id;
        $prepare->close();
        return $idGenerated;
    }

    private function checkDataRegistry($data)
    {
        //fullname
        if (strlen($data["fullname"]) < 5) {
            throw new Exception("Campo 'fullname' deve ter no mínimo 5 caracteres.");
        }
        //birthday
        $date = explode('/', trim($data["birthday"], '/'));
        if (count($date) < 3) {
            throw new Exception("Campo 'birthday' não está correto. Informe Dia/Mes/Ano.");
        }
        list($dia, $mes, $ano) = $date;
        if (!checkdate((int) $mes, (int) $dia, (int) $ano)) {
            throw new Exception("Campo 'birthday' não recebeu uma data válida.");
        }
        if (strlen($data["bio"]) < 5) {
            throw new Exception("Campo 'bio' deve ter no mínimo 5 caracteres.");
        }
        if (strlen($data["address"]) < 5) {
            throw new Exception("Campo 'address' deve ter no mínimo 5 caracteres.");
        }
        if (strlen($data["imageURL"]) < 5) {
            throw new Exception("Campo 'address' deve ter no mínimo 5 caracteres.");
        }
    }

    public function updateUser($id, $data = []): bool
    {
        if ($id == null) {
            throw new Exception("Um ID deve ser passado como parâmetro na URL.");
        }
        if (!is_numeric($id)) {
            throw new Exception("o ID deve ser um número.");
        }
        if (key_exists("id", $data)) {
            throw new Exception("Você não pode atualizar o id do usuário.");
        }

        return $this->prepareAndUpdateUser($id, $data);
    }

    function prepareAndUpdateUser($id, $data)
    {
        $query = "UPDATE users SET ";
        $query .= implode(", ", array_map(function ($key) {
            return "$key = ?";
        }, array_keys($data)));
        $prepare = $this->con->prepare("$query WHERE id = ?");
        $values = array_values($data);
        array_push($values, $id);
        $keyCount = str_repeat("s", count($values));
        $prepare->bind_param($keyCount, ...$values);
        $prepare->execute();
        $affected_rows = $prepare->affected_rows;
        $prepare->close();
        return $affected_rows > 0;
    }

    public function deleteUser($id): bool
    {
        $prepare = $this->con->prepare("DELETE FROM users WHERE id = ?");
        $prepare->bind_param("i", $id);
        $prepare->execute();
        $rowsAffected = $prepare->affected_rows;
        $prepare->close();

        if ($rowsAffected < 1) {
            throw new Exception("Usuário não deletado.");
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