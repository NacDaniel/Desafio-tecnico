![](https://i.imgur.com/pjtwSWS.png)


## Documentação da API

#### Retorna todos os usuários

```http
  GET /usuario
```

#### Retorna um único usuário

```http
  GET /usuario/${id}
```
#### Adiciona um usuário

```http
  POST /usuario
```

Corpo da requisição
| Parâmetro  | Tipo | Descrição |
| :---------- | :--------- | :------------------------------------------ |
| `fullname`      | `String` | **Obrigatório**. Nome completo do usuário |
| `birthday`      | `String` | **Obrigatório**. Timestamp da data de nascimento do usuário|
| `bio`      | `String` | **Obrigatório**. Biografia do usuário|
| `address`      | `String` | **Obrigatório**. Endereço do Usuário. Separe por ponto e vírgula. Rua Tenente Dias; 20; Recife; Pernambuco; Brasil
| `imageURL`      | `String` | **Obrigatório**. URL da imagem. Preferenciamente no Google Drive|

#### Deletar um usuário

```http
  POST /usuario/{id}
```

