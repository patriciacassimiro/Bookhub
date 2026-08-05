
//$API_KEY = "";
<?php
$titulo_pagina = "Importar livros; 
include 'includes/conexao.php';

$API_KEY = "AIzaSyBNP_Vb6_z-2f5NR340NC2PmMbX9Lkb3As";

$categorias = [
    "fiction",
    "romance",
    "fantasy",
    "history",
    "science",
    "technology",
    "business",
    "psychology",
    "biography",
    "children"
];

$total = 0;

foreach ($categorias as $categoria) {

    echo "<h2>Categoria: $categoria</h2>";

    
    for ($inicio = 0; $inicio <= 160; $inicio += 40) {

        echo "Importando livros " . ($inicio + 1) . " até " . ($inicio + 40) . "<br>";

        $url = "https://www.googleapis.com/books/v1/volumes?q=subject:$categoria&orderBy=newest&maxResults=40&startIndex=$inicio&key=$API_KEY";

        $json = @file_get_contents($url);

        if ($json === false) {
            echo "<span style='color:red'>Erro ao consultar a API.</span><br><br>";
            continue;
        }

        $dados = json_decode($json, true);

        if (!isset($dados["items"])) {
            echo "Nenhum livro encontrado.<br><br>";
            continue;
        }

        foreach ($dados["items"] as $item) {

            $google_id = $item["id"] ?? "";

            if ($google_id == "") {
                continue;
            }

            $titulo = $item["volumeInfo"]["title"] ?? "";

            $autor = isset($item["volumeInfo"]["authors"])
                ? implode(", ", $item["volumeInfo"]["authors"])
                : "Autor desconhecido";

            $descricao = $item["volumeInfo"]["description"] ?? "";

            $imagem = "";

            if (isset($item["volumeInfo"]["imageLinks"]["thumbnail"])) {
                $imagem = str_replace(
                    "http://",
                    "https://",
                    $item["volumeInfo"]["imageLinks"]["thumbnail"]
                );
            }

            $categoriaLivro = isset($item["volumeInfo"]["categories"])
                ? implode(", ", $item["volumeInfo"]["categories"])
                : $categoria;

            $idioma = $item["volumeInfo"]["language"] ?? "";

            $paginas = $item["volumeInfo"]["pageCount"] ?? 0;

            $link = $item["volumeInfo"]["infoLink"] ?? "";

            if (
                isset($item["saleInfo"]["retailPrice"]["amount"])
            ) {

                $preco = $item["saleInfo"]["retailPrice"]["amount"];

            } else {

                $preco = rand(8,35) + 0.90;

            }

            $sql = $conexao->prepare("
                INSERT IGNORE INTO livros
                (
                    google_id,
                    titulo,
                    autor,
                    descricao,
                    imagem,
                    preco,
                    categoria,
                    idioma,
                    paginas,
                    link_google
                )
                VALUES
                (?,?,?,?,?,?,?,?,?,?)
            ");

            $sql->bind_param(
                "sssssdssis",
                $google_id,
                $titulo,
                $autor,
                $descricao,
                $imagem,
                $preco,
                $categoriaLivro,
                $idioma,
                $paginas,
                $link
            );

            if ($sql->execute()) {

                if ($sql->affected_rows > 0) {

                    $total++;

                }

            }

        }

        echo "Página concluída.<br><br>";
        sleep(1);
    
    echo "<hr>";

echo "<h1 style='color:green'>Importação concluída!</h1>";

echo "<h2>Total de livros adicionados: $total</h2>";