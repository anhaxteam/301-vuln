<?php
  /*
  * ------ 301 REDIRECT -------
  * Desenvolvido por João Artur
  * anhax @2017
  * www.github.com/JoaoArtur
  */
  error_reporting(0);

  abstract class anhax {
    public static $argumento;
    public static $comandos;
    public static $url;
    public static $lista;
    public static $vulneraveis = array();

    public static function iniciar() {
      global $argv;

      echo "\033[31m[>] anhax 301 vuln finder
[>] buscar caminhos vulneráveis em páginas de administração
[>] developed by João Artur (K3N1)\033[1m\n\n";


      self::comandos();
      self::verificarArgumentos($argv);
    }
    public static function comandos() {
      self::$comandos['-u'] = 'url';
      self::$comandos['-l'] = 'lista';
    }
    public static function lista($lista) {
      if (file_exists($lista)) {
        self::$lista = $lista;
        echo "Lista: ".$lista."\n";
      }
    }
    public static function url($url) {
      self::$url = $url;
      echo "Url: ".$url."\n";
    }
    public static function ajuda() {
      echo "-u    =    Definir URL do site\n";
      echo "-l    =    Definir caminho da lista de arquivos\n";
    }
    public static function executar301() {
      if (self::$lista != null and self::$url != null) {
        $lista = explode("\n",file_get_contents(self::$lista));
        echo "\n[>] ".count($lista)." caminhos carregados\n[>] Iniciando...\n\n";

        foreach ($lista as $url) {
          $site = self::$url.$url;
          $cabecalho = get_headers($site);

          if (isset($cabecalho) && isset($cabecalho[0])) {
            if (strstr($cabecalho[0],"301") or strstr($cabecalho[0],"302")) {
              $inicio = curl_init();
              curl_setopt($inicio, CURLOPT_URL, $site);
              curl_setopt($inicio, CURLOPT_HEADER, 0);
              curl_setopt($inicio, CURLOPT_RETURNTRANSFER, TRUE);

              $conteudo = curl_exec($inicio);
              if ($conteudo != null) {
                echo "[+] $site    VULNERÁVEL\n";
                self::$vulneraveis[] = $site;
              }

              curl_close($inicio);

            }
          } else {
            echo "[+] Verifique se você preencheu todas as dependências corretamente.\n";
          }
        }
        echo "\n".count(self::$vulneraveis)." caminhos vulneráveis encontrados.\n";
      } else {
        echo "[+] Verifique se você preencheu todas as dependências corretamente.\n";
      }
    }
    public static function verificarArgumentos($argumentos) {
      if (count($argumentos) == 1) {
        self::ajuda();
      } else {
        unset($argumentos[0]);
        foreach ($argumentos as $key => $value) {
          if (isset(self::$comandos[$value])) {
            $atual_cmd = self::$comandos[$value];
            $atual_arg = $key;
            if (isset($argumentos[$atual_arg+1])) {
              self::$atual_cmd($argumentos[$atual_arg+1]);
            } else {
              echo "[+] Verifique se você preencheu todas as dependências corretamente.\n";
            }
          }
        }
        self::executar301();
      }
    }

  }

  anhax::iniciar();

?>
