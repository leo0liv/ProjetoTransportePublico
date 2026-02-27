<?php
// Definindo variáveis para conexão
$hostname_conn  =   "localhost";
$database_conn  =   "iwanez83_TransportePublico_ti19";
$username_conn  =   "iwanez83_TransportePublico_ti19";
$password_conn  =   "senacti19";
$charset_conn   =   "utf8";


// 1. CONEXÃO MYSQLI
$conn  =   
    new mysqli(
        $hostname_conn,
        $username_conn,
        $password_conn,
        $database_conn
    );
// Definir o conjunto de caracteres da conexão
mysqli_set_charset($conn,$charset_conn);

// Verificando possíveis erros na conexão
if($conn->connect_error){
    echo "Error: ".$conn->connect_error;
};

// 2. CONEXÃO PDO
try {
    $dsn = "mysql:host=$hostname_conn;dbname=$database_conn;charset=$charset_conn";
    $pdo = new PDO($dsn, $username_conn, $password_conn, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    echo "Error PDO: " . $e->getMessage();
}
?>