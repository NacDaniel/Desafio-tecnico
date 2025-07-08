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

        $query = $this->con->query("SELECT * FROM users" . ($id ? " WHERE id = $id" : ""));
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

        $query = $this->getQueryUpdateUser($id, $data);
        $this->con->query($query);
        if ($this->con->affected_rows < 1) {
            throw new Exception("Ocorreu um erro ao tentar atualizar esse usuário.");
        }

        return true;
    }

    private function getQueryUpdateUser($id, $data)
    {
        $query = "UPDATE users SET ";
        $i = 0;
        $listToSQL = [];
        foreach ($data as $k => $v) {
            if ($k == "id") {
                throw new Exception("Você não pode atualizar o id do usuário.");
            }

            $i++;
            if ($i - 1 != count($data) && $i != 1) {
                $query .= ", ";
            }
            $query .= "$k = \"%s\"";
            array_push($listToSQL, is_numeric($v) ? $v : $this->con->real_escape_string($v));
        }

        $query .= " WHERE ID=%s";
        array_push($listToSQL, $id);
        $query = sprintf($query, ...$listToSQL);
        return $query;
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