<?php

require_once __DIR__ . "/../view/userView.php";
require_once __DIR__ . "/../model/userModel.php";



header("Access-Control-Allow-Origin: *"); // Permitir requisições de origens diferentes.

class userController
{
    public function __construct($data)
    {
        $auth = $_SERVER["HTTP_AUTHORIZATION"] ?? null;
        $argsURL = $_SERVER["QUERY_STRING"] ?? "";
        $argsHeader = $_SERVER["argv"] ?? null;
        $methodType = $_SERVER["REQUEST_METHOD"] ?? null;
        $url = explode('/', trim($_SERVER["REQUEST_URI"], '/'));
        $path = strtoupper($url[0]);
        $id = $url[1] ?? null;

        try {
            $body = file_get_contents('php://input');
            $body = json_decode($body != "" ? $body : "[]", true);
            if ($methodType === "GET") {
                $this->request_get($path, $id);
                return;
            }

            if ($methodType === "POST") {
                $this->request_post($path, $id, $body);
                return;
            }

            if ($methodType === "DELETE") {
                $this->request_delete($path, $id);
                return;
            }

            throw new Exception("Método não aceito.");

        } catch (Exception $e) {
            $this->response(["code" => 404, "message" => $e->getMessage()]);
        }

    }

    private function request_get($path, $userID): void
    {
        if ($path === "USUARIO") {
            $data = userModel::getInstance()->getUser($userID) ?? [];
            $this->response(["code" => 200, "message" => "Sucesso ao obter os dados.", "data" => $data ?? []]);
        } else if ($path === "") {
            userView::getInstance()->loadIndex();
        } else {
            $this->response(["code" => 404, "message" => "Página não encontrada."]);
        }
    }

    private function request_post($path, $id, $body = []): void
    {
        if (empty($body)) {
            throw new Exception("A requisição deve ter um corpo com as informações a serem adicionadas/atualizadas.");
        }

        if ($path === "USUARIO") {

            if ($id) {
                $req = userModel::getInstance()->updateUser($id, $body);
                if ($req) {
                    $this->response(["code" => 200, "message" => "Usuário atualizado com sucesso"]);
                } else {
                    $this->response(["code" => 400, "message" => "Não foi possível atualizar esse usuário."]);
                }
                return;
            }

            if (!(array_key_exists("fullname", $body) && array_key_exists("birthday", $body) && array_key_exists("bio", $body) && array_key_exists("bio", $body) && array_key_exists("address", $body) && array_key_exists("imageURL", $body))) {
                throw new Exception("A requisição deve passar em seu corpo informações como: 'fullname', 'birthday', 'bio', 'address', 'imageURL'");
            }

            $req = userModel::getInstance()->insertUser($body);
            if ($req) {
                $this->response(["code" => 200, "message" => "Usuário adicionado com sucesso!", "ID" => $req]);
            }

        } else {
            $this->response(["code" => 404, "message" => "Página não encontrada."]);
        }
    }

    private function request_delete($path, $id): void
    {
        if ($path === "USUARIO") {
            if (empty($id)) {
                $this->response(["code" => 400, "message" => "Informe o ID do usuário."]);
                return;
            }
            if (userModel::getInstance()->deleteUser($id)) {
                $this->response(["code" => 200, "message" => "Usuário foi deletado com sucesso."]);
            }
        } else {
            $this->response(["code" => 404, "message" => "Página não encontrada."]);
        }
    }

    public function response($data): bool
    {
        header("Content-Type: application/json");
        http_response_code($data["code"]);
        echo json_encode($data);
        return true;
    }

}

new userController($_SERVER);
?>