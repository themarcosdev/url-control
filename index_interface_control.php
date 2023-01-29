<?php
	require_once __DIR__."/index_control.php";
?>
<!DOCTYPE html>
<html lang="ptbr">
<?php
	$titulo_pag = "Controle de URLS";
	require_once __DIR__ . "/index_header.php";

	$configs = json_decode(getConfigs());
?>
<body class="bg-dark">
	<div class="container">
		<div class="row">
			<div class="container-fluid">
				<div class="row">
					<div class="col table-responsive mt-3">
						<div class="controls">
							<!-- div INSERT - novo URL / Sair -->
							<div class="d-flex col-12">
								<div class="col-6 ">
									<button id="btnCadastrarNovoURL" class="btn bg-primary text-white my-3">
										<i class="fa-solid fa-circle-plus"></i> CADASTRAR NOVO
									</button>
								</div>
								<div class="col-6  text-end">
									<a href="deslogar.php" class="btn bg-danger text-white" title="Deslogar">
										<i class="fa-regular fa-circle-left"></i> Sair
									</a>
								</div>
							</div>
						</div>
						<div class="infos d-flex">
							<div class="info1 col-6">
								<span class="text-white my-2"><b>Seus URL's cadastrados :</b></span>
							</div>
							<div class="info2 d-flex col-6">
								<input name="valorPesquisado" id="valorPesquisado" type="search" class="form-control" placeholder="Pesquise por um URL">
								<!-- <button type="button" class="btn bg-primary"><i class="fa-solid fa-magnifying-glass"></i></button> -->
							</div>
						</div>
						<table class="my-3 table table-striped table-dark">
							<thead class="bg-primary text-white text-center">
								<tr>
									<th>Id</th>
									<th>URL Customizado</th>
									<th class="text-center">Lv URL</th>
									<th style="max-width:10%;word-break: break-all;">Camindo do arquivo real no servidor</th>
									<th class="text-center">URL anterior</th>
									<th class="text-center">Tipo de conte&uacute;do solicitado</th>
									<th class="text-center">#</th>
									<th class="text-center">#</th>
								</tr>
							</thead>
							<tbody>
								<?php
									// Percorrendo cada url e exibindo na interface dentro da tabela ;
									$totalKeys = 0; 
									foreach($configs->urls as $i=>$config){
										if (isset($config->id) ) {
											$totalKeys += 1;
											echo "
													<tr class='text-center'>
														<td id='td$config->id'>$config->id</td>
														<td>$config->url_customizado</td>
														<td>$config->url_lv</td>
														<td>$config->caminho_no_server</td>
														<td>$config->url_anterior</td>
														<td>$config->tp_cont_solic</td>
														<td><button class='btn bg-success text-white btnEdit'> <i class='fa-solid fa-pen-to-square'></i> Editar</button></td>
														<td><button class='btn bg-danger text-white btnDel'> <i class='fa-solid fa-trash'></i> Deletar</button></td>
													</tr>
											";
										}
									}
									if($totalKeys == 0){
										echo "
											<tr>
												<td colspan='8' class='text-center'> N&atilde;o h&aacute; registros</td>
											</tr>
										";
									}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<!-- Inclusão do  modal -->
				<div class="modal fade bd-example-modal-lg" data-bs-backdrop="static" id="modal-pagina" tabindex="-1" role="dialog" aria-labelledby="#btnCadastrarNovoURL" aria-hidden="true">
					<div class="modal-dialog modal-md">
						<div class="modal-content">
							<div class="modal-title"></div>
							<div class="modal-body">
								<div class="row">
									<div class="col-12 text-center">
										<h5 id="h5MsgModal">Cadastro de novo URL</h5>
									</div>
									<div id="divContModal" class="col-12">
										<div class="d-flex controls2 border p-2 my-5 ">
											<!-- Form de cadastro / cadastrando novo URL -->
											<form class="col-10 col-sm-8 col-lg-10 mx-5 p-1" action="setConfigs.php" method="POST">
												<div class="form-floating">
													<input id="inpNovoURL" name="inpNovoURL" type="text" class="my-2 form-control" required autocomplete="off">
													<label for="inpNovoURL" class="fw-bold">Digite o URL Customizado</label>
												</div>
												<div class="form-floating">
													<input id="inpNovoLvURL" name="inpNovoLvURL" type="number" class="my-2 form-control" min="0" max="50" required autocomplete="off">
													<label for="inpNovoLvURL" class="fw-bold">Digite Lv URL</label>
												</div>
												<div class="form-floating">
													<input id="inpNovoURLAnterior" name="inpNovoURLAnterior" type="text" class="my-2 form-control" required autocomplete="off">
													<label for="inpNovoURLAnterior" class="fw-bold">Digite o URL anterior</label>
												</div>
												<div class="form-floating">
													<input id="inpNovoCaminhoServer" name="inpNovoCaminhoServer" type="text" class="my-2 form-control" required autocomplete="off">
													<label for="inpNovoCaminhoServer" class="fw-bold">Digite o caminho do index ao arquivo real</label>
												</div>
												<div class="form-floating">
													<select id="selecTipoSolicit" name="selecTipoSolicit" class="my-2 form-control" required>
														<option value="">SELECIONE</option>
														<option value="include">include</option>
														<option value="include_once">include_once</option>
														<option value="require">require</option>
														<option value="require_once">require_once</option>
													</select>
													<label for="selecTipoSolicit" class="fw-bold">Selecione o tipo de solicita&ccedil;&atilde;o de conte&uacute;do</label>
												</div>
												<div class="text-center">
													<button class="btn small bg-primary text-white"><i class="fa-solid fa-floppy-disk"></i> Salvar</button>
												</div>
											</form>
											<!-- fechando cadastro URL -->
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Termino do Modal ; -->
			</div>
		</div>
	</div>
</body>
</html>
<div class="spinner d-none"></div>
<script>
	document.getElementById("btnCadastrarNovoURL").addEventListener("click",()=>{
		$("#modal-pagina").modal('show');
	});
</script>
