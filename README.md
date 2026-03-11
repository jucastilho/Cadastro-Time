# Como executar o projeto em seu computador

Este projeto foi desenvolvido em **PHP** e utiliza **MySQL** para o banco de dados.
Para executá-lo corretamente, é necessário utilizar um servidor local, como o **XAMPP**.

## 1. Baixar o projeto

1. Baixe ou clone este repositório para o seu computador.
2. Após o download, extraia a pasta 'fut' de dentro (pois não funciona em formato `.zip`.)

## 2. Mover a pasta do projeto

1. Copie a pasta do projeto (**fut**).
2. Cole a pasta do projeto dentro da pasta **htdocs**. (Este Comptador -> Xampp -> htdocs)

## 3. Iniciar o servidor local

1. Abra o **XAMPP Control Panel**.
2. Clique em **Start** nos serviços:

   * **Apache**
   * **MySQL**

Os dois devem ficar com o status **Running**.

## 4. Criar ou importar o banco de dados

1. Abra o navegador e acesse: http://localhost/phpmyadmin
2. No menu lateral, clique em **Novo** (New).
3. Digite o nome do banco de dados utilizado no projeto (futebol).
4. Clique em **Criar**.

### Importar banco de dados (arquivo .sql)

1. Clique no banco de dados que você criou.
2. Vá na aba **Importar**.
3. Clique em **Escolher arquivo**.
4. Selecione o arquivo `banco.sql` do projeto.
5. Clique em **Executar**.

Isso criará automaticamente as tabelas necessárias.

## 5. Executar o projeto

1. Abra o navegador.
2. Digite o seguinte endereço: http://localhost/fut

*(fut é o nome da pasta do projeto dentro do htdocs)*

3. O sistema será carregado e estará pronto para uso.

## Observações

* Certifique-se de que **Apache e MySQL estão ativos no XAMPP**.
* O nome da pasta no **htdocs** deve ser o mesmo utilizado na URL do localhost.
* Caso ocorra erro de conexão com o banco, verifique as configurações no arquivo de conexão do projeto (usuário, senha e nome do banco).
