<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_thickbox();
?>
<script type="text/javascript">
	function tb_show_logTc(data){
		tb_show('Dados Request','<?php echo get_site_url() ?>?wc_yapay_intermediador_log=1&log_data='+data,'');
		//console.debug(data);
		//setTimeout(	function(){console.debug(data);document.getElementById("TB_ajaxContent").innerHtml = data},4000);
	}
</script>
<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <?php settings_errors(); ?>
    <form method="post" action="options.php">
        <?php
        	$requests = new WC_Yapay_Intermediador_Requests();
        	$responses = new WC_Yapay_Intermediador_Responses();
        ?>
    
    	<h2>Logs Yapay Intermediador</h2>

    	<h4>Requests</h4>
    	<table class="wp-list-table widefat fixed striped pages">
			<thead>
			<tr>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						ID
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Endpoint
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Data / Hora
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Parâmetros
					</td>
				</tr>
			</tr>
			</thead>

			<tbody id="the-list">
				<?php 
					$requestsData = $requests->getRequests();
					foreach ($requestsData as $requestData) {	
					
				?>
				<tr id="post-5" class="iedit author-self level-0 post-5 type-page status-publish hentry">
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo $requestData->control_id;
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo str_replace("https://api.traycheckout.com.br/", "", str_replace("https://api.sandbox.traycheckout.com.br/", "", $requestData->request_url));
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo $requestData->date_request;
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<a href="javascript:void(0)" onclick="tb_show_logTc('<?php echo urlencode($requestData->request_params); ?>')">Ver Detalhes </a>
					</td>
				</tr>
				<?php
					}
				?>
			</tbody>

			<tfoot>
			<tr>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						ID
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Endpoint
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Data / Hora
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Parâmetros
					</td>
				</tr>	
			</tr>
			</tfoot>

		</table>

    	<h4>Responses</h4>
    	<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						ID
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Endpoint
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Data / Hora
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Parâmetros
					</td>
				</tr>
			</thead>

			<tbody id="the-list">
				<?php 
					$responsesData = $responses->getResponses();
					foreach ($responsesData as $responseData) {	
					
				?>
				<tr id="post-5" class="iedit author-self level-0 post-5 type-page status-publish hentry">
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo $responseData->control_id;
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo str_replace("https://api.traycheckout.com.br/", "", str_replace("https://api.sandbox.traycheckout.com.br/", "", $responseData->response_url));
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<?php 
							echo $responseData->date_response;
						?>
					</td>
					<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
						<a href="javascript:void(0)" onclick="tb_show_logTc('<?php echo urlencode($responseData->response_body); ?>')">Ver Detalhes </a>
					</td>
				</tr>
				<?php
					}
				?>
			</tbody>

			<tfoot>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						ID
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Endpoint
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Data / Hora
					</td>
					<td id="cb" class="manage-column column-cb check-column">
						Parâmetros
					</td>
				</tr>	
			</tfoot>

		</table>
        

    </form>

</div>
