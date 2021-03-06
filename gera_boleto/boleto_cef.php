<?php
@session_start();
// +----------------------------------------------------------------------+
// | BoletoPhp - Vers�o Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo est� dispon�vel sob a Licen�a GPL dispon�vel pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Voc� deve ter recebido uma c�pia da GNU Public License junto com     |
// | esse pacote; se n�o, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colabora��es de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de Jo�o Prado Maia e Pablo Martins F. Costa				  |
// | 																	  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordena��o Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto CEF: Elizeu Alcantara         		          |
// +----------------------------------------------------------------------+


// ------------------------- DADOS DIN�MICOS DO SEU CLIENTE PARA A GERA��O DO BOLETO (FIXO OU VIA GET) -------------------- //
// Os valores abaixo podem ser colocados manualmente ou ajustados p/ formul�rio c/ POST, GET ou de BD (MySql,Postgre,etc)	//

require('../util/conecta.php');
require('gera_boleto.php');

$boleto = new  gera_boleto();
$boleto->busca_dados();
$boleto->registros = $boleto->resultado->FetchNextObject();

// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 5;
$taxa_boleto = 3;
$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = $boleto->reg_soma->SOMA;
//"2950,00"; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["inicio_nosso_numero"] = "80";  // Carteira SR: 80, 81 ou 82  -  Carteira CR: 90 (Confirmar com gerente qual usar)
$dadosboleto["nosso_numero"] = "19525086";  // Nosso numero sem o DV - REGRA: M�ximo de 8 caracteres!
$dadosboleto["numero_documento"] = "27.030195.10";	// Num do pedido ou do documento
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emiss�o do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com v�rgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $boleto->registros->CLI_NOME;
//"Nome do seu Cliente";
$dadosboleto["endereco1"] = $boleto->registros->CLI_ENDERECO;

//$dadosboleto["endereco2"] = $boleto->registros->CID_DESCRICAO." - ".$boleto->registros->CID_UF." Cep: ".$boleto->registros->CID_UF;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Pagamento de Compras Sites";
$dadosboleto["demonstrativo2"] = "Compra referente ao pedido ".$_SESSION['sessao_codigo_pedido'] ;;
$dadosboleto["demonstrativo3"] = "";

// INSTRU��ES PARA O CAIXA
$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% ap�s o vencimento";
$dadosboleto["instrucoes2"] = "- Receber at� 10 dias ap�s o vencimento";
$dadosboleto["instrucoes3"] = "- Em caso de d�vidas entre em contato conosco: betobr12@yahoo.com.br";
$dadosboleto["instrucoes4"] = "";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "";		
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "";

// ---------------------- DADOS FIXOS DE CONFIGURA��O DO SEU BOLETO --------------- //

// DADOS DA SUA CONTA - CEF
$dadosboleto["agencia"] = "1231"; // Num da agencia, sem digito
$dadosboleto["conta"] = "0233987"; 	// Num da conta, sem digito
$dadosboleto["conta_dv"] = "0"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - CEF
$dadosboleto["conta_cedente"] = "0"; // ContaCedente do Cliente, sem digito (Somente N�meros)
$dadosboleto["conta_cedente_dv"] = "3"; // Digito da ContaCedente do Cliente
$dadosboleto["carteira"] = "SR";  // C�digo da Carteira: pode ser SR (Sem Registro) ou CR (Com Registro) - (Confirmar com gerente qual usar)

// SEUS DADOS
$dadosboleto["identificacao"] = "Roberto Carlos Rodrigues dos Santos";
$dadosboleto["cpf_cnpj"] = "391.485.068.00";
$dadosboleto["endereco"] = "Rua Francisco Foot, predio 17";
$dadosboleto["cidade_uf"] = "SP";
$dadosboleto["cedente"] = "Roberto Carlos Rodrigues dos Santos";

// N�O ALTERAR!
include("include/funcoes_cef.php"); 
include("include/layout_cef.php");
?>
