<?php
	if(isset($_POST['salvar'])){
		//Variáveis de conexão com o banco de dados		
		require_once("../scripts/connectvars.php");
		
		//Verificação dos campos
		require_once("../scripts/verify_empty_regex.php");
		  
			//------------------== NOME ==-----------------------		
			//==================REQUISITOS=======================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Retirar os espaços em branco extras
			#3 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:
			$regex_nome = '/^[a-zA-Zá-üÁ-Ü]+(( [a-zA-Zá-üÁ-Ü]+)+)?$/';
			//====================Início=========================
			//Retira os caracteres em branco das bordas
			$nome = trim($_POST['cmp_nome']); 
					
			//Laço para retirar os espaços em branco extras
			while(strpos($nome, "  ") != 0){
				$nome = str_replace("  ", " ", $nome);
			}
			
			VerificaVazioRegex("Nome", $nome, $regex_nome);
			//----------------=Fim - Nome=---------------------------
			
			
			//------------------== EMAIL ==--------------------------		
			//==================REQUISITOS===========================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Verificar se o sistema já possui o email inserido
			#3 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:
			$regex_email = '/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9_.-]+\.[a-zA-Z0-9_.-]{2,4}$/';
			//====================Início=========================
			//Retira os caracteres em branco das bordas
			$email = trim($_POST['cmp_email']);
			
			//Verificação da presença do email no banco de dados
			if (preg_match($regex_email, $email)){
				$verify_email = "SELECT email FROM users WHERE email = '$email'";
				$verify_email_result = mysqli_query(GetMyConnection(), $verify_email)
				or die ("Erro na verificação do email!");
				CleanUpDB();
				$linha_verify_email = mysqli_fetch_assoc($verify_email_result);
				if (!empty($linha_verify_email))
					$erros["Email"] = "já está cadastrado, insira outro email ou recupere sua conta.";			
				else 
					VerificaVazioRegex("Email", $email, $regex_email);	
			}			
			//----------------=Fim - Email=---------------------------
			
					
			//------------------== SEXO ==--------------------------		
			//==================REQUISITOS===========================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:*/		
			$regex_sexo = '/^[f,m]$/';
			//====================Início===============================		
			//Retira os caracteres em branco das bordas
			$sexo = trim($_POST['cmp_sexo']);
			
			VerificaVazioRegex("Sexo", $sexo, $regex_sexo);
			//----------------=Fim - Sexo=---------------------------
			
			
			//------------------== TELEFONE ==--------------------------		
			//==================REQUISITOS===========================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:*/		
			$regex_tel = "/^\([0-9]{2}\)\s[0-9]{4,5}-[0-9]{4,5}$/";
			//====================Início===============================		
			//Retira os caracteres em branco das bordas
			$tel = trim($_POST['cmp_cel']);
			
			VerificaVazioRegex("Telefone", $tel, $regex_tel);
			//----------------=Fim - Telefone=---------------------------
			
			
			//--------------== DATA DE NASCIMENTO ==---------------------		
			//==================REQUISITOS===========================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:		
			$regex_nasc = '/^(19|20)\d{2}-([0][1-9]|[1][0-2])-([0][1-9]|[12][0-9]|[3][01])$/';
			//====================Início===============================		
			//Retira os caracteres em branco das bordas
			$nasc = trim($_POST['cmp_nasc']);
			
			VerificaVazioRegex("Data de Nascimento", $nasc, $regex_nasc);
			//--------------=Fim - Data de Nascimento=--------------------
			
			
			//------------------== SENHA ==--------------------------		
			//==================REQUISITOS===========================
			#1 - Retirar os caracteres em branco das bordas
			#2 - Verificar se as senhas fornecidas são iguais
			#3 - Verificar se a string fornecida encontra-se 
			//   de acordo com a regex:		
			$regex_senha = '/^[^ ]{8,}$/';
			//====================Início===============================		
			//Retira os caracteres em branco das bordas
			$senha = trim($_POST['cmp_senha']);
			$senha_confirm = trim($_POST['cmp_senha_confirm']);
			
			//Verifica se as senhas são iguais
			if ($senha == $senha_confirm){
				VerificaVazioRegex("Senha", $senha, $regex_senha);
			} else {
				$erros["Senha"] = "não coincidem.";
			}		
			//----------------=Fim - Senha=---------------------------
		//----------=Fim - Verificação dos Dados=-----------------
		
		
		  /*==================================================//
		 //             VERIFICAÇÃO DE PENDÊNCIAS            //
		//==================================================*/
		//Iteração para verificar as inconsistências no preenchimento
		//	do formulário		
		if (!empty($erros)){
			echo "As seguintes pendências foram encontradas:<br />";
			foreach ($erros as $chave => $valor){
				echo "O campo <span> $chave</span> $valor<br />";
			}
		}		
		//----------=Fim - Verificação de Pendências=-----------------
		
		
		  /*==================================================//
		 //              ARMAZENAMENTO DOS DADOS             //
		//==================================================*/		
		//Se não houver erros os dados são armazenados no banco de dados
		if (empty($erros)){
			//Criptografa a senha antes de armazená-las
			$new_senha = sha1($senha);
			
			$query = "INSERT INTO users(id, nome, email, sexo, telefone, nascimento, senha, data_cad, user_status) values
						(null, '$nome', '$email', '$sexo', '$tel', '$nasc','$new_senha', NOW(), 1)";
			
			$exec = mysqli_query(GetMyConnection(), $query) or die ("Erro ao salvar dados!");	
			
			CleanUpDB();
			
			$save_dados_bd = TRUE;	
			
			echo "Os dados foram salvos com sucesso!";
			
		}
	}
?>	
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8" />
	<title>Novo Cadastro</title>
	<link href="/css/reset.css" rel="stylesheet"/>
	<link href="/css/style.css" rel="stylesheet"/>
	<script src="../js/jquery-2.1.1.js" type="text/javascript"></script>
	<script src="../js/jquery.mask.js" type="text/javascript"></script>
	<script src="../scripts/balloon.js" type="text/javascript"></script>
	<script>
		window.onload = function(){			
	  		var password = document.getElementById("cmp_senha"),
	  		confirm_password = document.getElementById("cmp_senha_confirm");	
			password.onkeyup = verificaSenha;
			confirm_password.onkeyup = verificaSenha;			
			$("#salvar").attr("disabled","disabled");			
		}
		
		function verificaSenha(){
			var password = document.getElementById("cmp_senha"),
		  		confirm_password = document.getElementById("cmp_senha_confirm");	
			if ((password.value != confirm_password.value) ||
				password.value == "" ||
				password.value == null ||
				password.value.length < 8)				
				$("#salvar").attr("disabled","disabled");	
			else				
				$("#salvar").removeAttr("disabled");	
		}		
		
		var options =  {
			  onKeyPress: function(phone, e, field, options) {
			    var masks = ['(00) 0000-0000', '(00) 00000-0000'];
			    var mask = (phone.length>14) ? masks[1] : masks[2];
			    $('#cmp_cel').mask(mask, options);
			}};			
			
		$(document).ready(function(){			
			$('#cmp_cel').mask('(00) 0000-00000', options);
		});
		
		function mask(){			
				var tecla = event.keyCode;
				if (tecla == 8){
					$(document).ready(function(){
						$('#cmp_cel').mask('(00) 0000-00000', options);
					});	
				}				
		}
	</script>
	<style>
		body {
			position: relative;
		}
		input + span {
			padding-right: 30px;
		}
		input:invalid+span:after{			
			position: absolute;
			content: '✖';
			padding-left: 5px;
			color: #8b0000;
		}
		input:valid+span:after {
			position: absolute;
			content: '✓';
			padding-left: 5px;
			color: #009000;
		}		
	</style>
</head>
<body>
	<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" autocomplete="on">
		<p>Preencha as informações corretamente, pois serão necessárias caso você perca seu acesso</p>
		
		<label for="cmp_nome">Nome:</label>
		<input
			type="text"
			name="cmp_nome"
			id="cmp_nome"
			autofocus="autofocus"
			required="required"
			pattern="^[a-zA-Zá-üÁ-Ü ]+$"
			value="<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE) echo $nome; ?>"
		/>
		<span class="validity"></span><br />
			
						
		<label for="cmp_email">Email:</label>
		<input
			type="email"
			name="cmp_email"
			id="cmp_email"
			required="required"
			pattern="^[a-zA-Z0-9_.-]+@[a-zA-Z0-9_.-]+\.[a-zA-Z0-9_.-]{2,4}$"
			value="<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE) echo $email; ?>"
		/>
		<span class="validity"></span><br />
		
				
		<label for="cmp_sexo">Sexo:</label>
		<select
			id="cmp_sexo"
			name="cmp_sexo"
			required="required">
			<option value="m"
			<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE && $sexo == 'm') echo 'selected="selected"'; ?>>Masculino</option>
			<option value="f"
			<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE && $sexo == 'f') echo 'selected="selected"'; ?>>Feminino</option>
		</select><br />
			
					
		<label for="cmp_tel">Telefone:</label>
		<input
			type="tel"
			name="cmp_cel"
			id="cmp_cel"
			onkeyup="mask()"
			maxlength="15"
			required="required"
			placeholder="Insira apenas números..."
			value="<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE) echo preg_replace('/[() -]/','',$tel); ?>"
			pattern="^[0-9() -]{14,15}$"
		/>
		<span class="validity"></span><br />
		
		
		<label for="cmp_nasc">Data de Nascimento:</label>
		<input
			type="date"
			name="cmp_nasc"
			id="cmp_nasc"
			required="required"
			value="<?php if(isset($_POST['salvar']) && $save_dados_bd == FALSE) echo $nasc; ?>"
			pattern="^[0-2]+$"
		/><br />
		
		
		<label for="cmp_senha">Senha:</label>
		<input
			type="password"
			name="cmp_senha"
			id="cmp_senha"
			required="required"
			pattern="^[^ ]{8,}$"
			oninvalid="setCustomValidity('Você inseriu caracteres inválidos ou insuficientes!')"
			onkeypress="try{setCustomValidity('')}catch(e){}"
			onchange="try{setCustomValidity('')}catch(e){}"		
			placeholder="Mínimo 8 caracteres..."
		/>
		<span class="validity"></span><br />
		
		
		<label for="cmp_senha_confirm">Confirme sua senha:</label>
		<input
			type="password"
			name="cmp_senha_confirm"
			id="cmp_senha_confirm"
			required="required"
			pattern="^[^ ]{8,}$"
			title="Repita sua senha!"
		/><br />
				
		<input
			type="submit"
			id="salvar"
			name="salvar"
			value="Cadastrar"
		/>
	</form>	
</body>
</html>