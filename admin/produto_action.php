<?php 

require_once '../includes/connect.php';

/**
 * Negativar todas as ofertas do produto
 * Atualizar ofertas, ou;
 * Inserir novas ofertas;
 * 
 * @param type $pdo
 * @param type $controle
 * @param type $data
 */
function alterar_produto_preco($pdo, $controle, $data = array()){

    # Inativa todas as ofertas do produto
    $pdo->exec("UPDATE PRODUTOPRECO SET STATUS = 0 WHERE PRODUTO_CONTROLE = {$controle}");

    foreach ($data as $key => $value):
        # Atualiza a oferta passada no formulário
        if(!$pdo->exec("UPDATE PRODUTOPRECO SET ".
               " PRECO  = '{$value['preco']}',".
               " STATUS = 1".
               " WHERE PRODUTOPRECO_CONTROLE = {$key} AND PRODUTO_CONTROLE = {$controle}")){
                   # Insere uma nova oferta
                   $pdo->exec("INSERT INTO PRODUTOPRECO(PRODUTO_CONTROLE, PRECO, STATUS) VALUES({$controle}, '{$value['preco']}', 1)");
               }
    endforeach;
    
}

if($_REQUEST != ""){
    
    if($_REQUEST['action'] == "cadastrar"){
        
        $nome      = utf8_decode(trataString($_POST['nome'     ]));
        $descricao = utf8_decode(trataString($_POST['descricao']));
        $ofertas   = $_POST['oferta'];
        
        try {
            
            if(!$rst = $pdo->exec("INSERT INTO PRODUTO(NOME, DESCRICAO_LOJA, STATUS) VALUES('{$nome}','{$descricao}', 1)")){
                   
                    throw new \Exception('===> Erro de SQL <===');
                    
               }
               
               # Recupera o ID do último registro
               $sql = "SELECT TOP 1 PRODUTO_CONTROLE FROM PRODUTO ORDER BY PRODUTO_CONTROLE DESC";
               $result = $pdo->query($sql);
               $total_registros = $result->fetch();
               $controle = $total_registros['PRODUTO_CONTROLE'];
               
               alterar_produto_preco($pdo, $controle, $ofertas);
               
               $_SESSION['confirm'] = "Cadastrado";
               header("Location: produto.php");
               
        } catch (PDOException $Exceptio) {
            
            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
            
        }
        
    }
    
    if($_REQUEST['action'] == "alterar"){
                        
        $controle  = $_POST['controle' ];
        $nome      = utf8_decode(trataString($_POST['nome'     ]));
        $descricao = utf8_decode(trataString($_POST['descricao']));
        $ofertas   = $_POST['oferta'];
        
        try {
            if(!$pdo->exec("UPDATE PRODUTO SET ".
               " DESCRICAO_LOJA         = '{$descricao}',". 
               " NOME                   = '{$nome}'".
               " WHERE PRODUTO_CONTROLE = {$controle}")){
                   
                    throw new \Exception('===> Erro de SQL <===');
                    
               }
               
               alterar_produto_preco($pdo, $controle, $ofertas);
               
               $_SESSION['confirm'] = "Alterado";
               header("Location: produto.php");
               
        } catch (PDOException $Exceptio) {
            
            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
            
        }
        
    }
    
    if($_REQUEST['action'] == "excluir"){
        
                
        $controle  = $_GET['controle' ];
        
        try {
            if(!$pdo->exec("UPDATE PRODUTO SET ".
               " STATUS  = '0'".
               " WHERE PRODUTO_CONTROLE = {$controle}")){
                   
                    throw new \Exception('===> Erro de SQL <===');
                    
               }
               
               $_SESSION['confirm'] = "Alterado";
               header("Location: produto.php");
               
        } catch (PDOException $Exceptio) {
            
            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
            
        }
        
    }
    
    //restaurar
    if($_REQUEST['action'] == "restaurar"){
        
                
        $controle  = $_GET['controle' ];
        
        try {
            if(!$pdo->exec("UPDATE PRODUTO SET ".
               " STATUS  = '1'".
               " WHERE PRODUTO_CONTROLE = {$controle}")){
                   
                    throw new \Exception('===> Erro de SQL <===');
                    
               }
               
               $_SESSION['confirm'] = "Alterado";
               header("Location: produto_excluido.php");
               
        } catch (PDOException $Exceptio) {
            
            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
            
        }
        
    }
    
    if($_POST['action'] == "upload_imagem"){
        
        if ( isset( $_FILES[ 'arquivo' ][ 'name' ] ) && $_FILES[ 'arquivo' ][ 'error' ] == 0 ) {

        $arquivo_tmp = $_FILES['arquivo'  ][ 'tmp_name'];
        $nome        = $_FILES['arquivo'  ][ 'name'    ];
        $controle    = $_POST ['controle' ];

        $extensao = pathinfo ($nome, PATHINFO_EXTENSION);
        $extensao = strtolower ($extensao);

        if (strstr('.jpg;.jpeg;.gif;.png', $extensao)) {

            $name    = uniqid(time()) . '.' . $extensao;
            $destino = '../data/produtos/'  . $name;

            if (@move_uploaded_file($arquivo_tmp, $destino)) {
                
                # Inativa todas as imagens do produto
                $pdo->exec("UPDATE PRODUTOIMAGEM SET STATUS = 0 WHERE PRODUTO_CONTROLE = {$controle}");
                
                # Insere a imagem
                $pdo->exec("INSERT INTO PRODUTOIMAGEM(PRODUTO_CONTROLE, NOME_IMAGEM, STATUS) VALUES({$controle}, '{$name}', 1)");

                $_SESSION['confirm'] = "Imagem salva";
                header("Location: produto.php");
                
            }
            else
                echo 'Erro ao salvar o arquivo. Aparentemente você não tem permissão de escrita.<br />';
        }
        else
            echo 'Você poderá enviar apenas arquivos "*.jpg;*.jpeg;*.gif;*.png"<br />';
        }
        else
            echo 'Você não enviou nenhum arquivo!';
    
    }
    
//    if($_REQUEST['action'] == "excluir"){
//        
//        $controle  = $_GET['controle'];
//        
//        try {
//            if(!$pdo->exec("DELETE FROM PRODUTO WHERE PRODUTO_CONTROLE = {$controle}")){
//                   
//                    throw new \Exception('===> Erro de SQL <===');
//                    
//               }
//               
//               $_SESSION['confirm'] = "Excluído";
//               header("Location: produto.php");
//               
//        } catch (PDOException $Exceptio) {
//            
//            throw new MyDatabaseException( $Exception->getMessage( ) , (int)$Exception->getCode( ) );
//            
//        }
//        
//    }
    
}
