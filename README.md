## Como rodar o projeto localmente com Docker Compose
Este projeto utiliza Docker Compose para orquestrar a aplicação e o banco de dados MySQL. As portas padrão utilizadas são:

- **Aplicação (HTTP):** porta `80`
- **Banco de dados (MySQL):** porta `3306`

Para iniciar o projeto, execute o comando abaixo:

#### Comando

```bash
sudo docker compose up -d --build
```


## Documentação da API

Projeto hospedado temporariamente no url http://listausuarios.infy.uk/

### Endpoints disponíveis

| Método | Rota            | Descrição                       |
|--------|------------------|----------------------------------|
| GET    | /usuario         | Lista todos os usuários         |
| GET    | /usuario/{id}    | Retorna dados de um usuário     |
| POST   | /usuario         | Cria um novo usuário            |
| POST   | /usuario/{id}    | Atualiza um usuário existente   |
| DELETE | /usuario/{id}    | Remove um usuário               |

#### Adiciona um usuário
```http
  POST /usuario/
```

| Parâmetro  | Tipo | Descrição |
| :---------- | :--------- | :------------------------------------------ |
| `fullname`      | `String` | **Obrigatório**. Nome completo do usuário |
| `birthday`      | `String` | **Obrigatório**. Data de nascimento no formato Dia-Mes-Ano|
| `bio`      | `String` | **Obrigatório**. Biografia do usuário|
| `address`      | `String` | **Obrigatório**. Endereço do Usuário. 
| `imageURL`      | `String` | **Obrigatório**. URL da imagem. |

#### Atualiza um usuário

```http
  POST /usuario/{id}
```

Parâmetros opcionais a serem passados no corpo da requisição. (Ao menos um deve ser informado)

| Parâmetro  | Tipo | Descrição |
| :---------- | :--------- | :------------------------------------------ |
 | `fullname`      | `String` | **Opcional**. Nome completo do usuário |
| `birthday`      | `String` | **Opcional**. Data de nascimento no formato Dia-Mes-Ano|
| `bio`      | `String` | **Opcional**. Biografia do usuário|
| `address`      | `String` | **Opcional**. Endereço do Usuário. 
| `imageURL`      | `String` | **Opcional**. URL da imagem. |

#### Deletar um usuário

```http
  DELETE /usuario/{id}
```










![](https://i.imgur.com/pjtwSWS.png)
