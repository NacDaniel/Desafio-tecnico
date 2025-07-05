<?php

require "./view/userView.php";
require "./model/userModel.php";

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

            if ($methodType === "GET") {
                $this->request_get($path, $id);
                return;
            }

            if ($methodType === "POST") {
                $this->request_post($path, $argsURL);
                return;
            }

            if ($methodType === "DELETE") {
                $this->request_delete($path, $id);
                return;
            }
        } catch (Exception $e) {
            $this->response(["code" => 404, "message" => $e->getMessage()]);
        }

    }

    private function request_get($path, $userID): void
    {
        if ($path === "USUARIO") {
            $data = userModel::getInstance()->getUser($userID) ?? [];
            if (!$data) {
                $this->response(["code" => 404, "message" => "Usuário(s) não encontrado(s)."]);
                return;
            }
            $this->response(["code" => 200, "message" => "Sucesso ao obter os dados.", "data" => $data]);
        } else if ($path === "") {
            // reqs do html
        } else {
            $this->response(["code" => 404, "message" => "Página não encontrada."]);
        }
    }

    private function request_post($path, $data = []): void
    {
        echo $path;
        if ($path === "USUARIO") {

        }
    }

    private function request_delete($path, $id): void
    {
        if ($path === "USUARIO") {
            if (empty($id)) {
                $this->response(["code" => 400, "message" => "Informe o ID do usuário."]);
                return;
            }
            $this->response(["code" => 200, "message" => "Usuário deletado."]);
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