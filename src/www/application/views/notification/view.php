<!DOCTYPE html>
<html>
<head>
<title>Notifications</title>

<script
  src="https://code.jquery.com/jquery-2.2.4.min.js"
  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
  crossorigin="anonymous"></script>

<link rel="stylesheet" type="text/css" href="/assets/css/bluenote.css">

<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-9ralMzdK1QYsk4yBY680hmsb4/hJ98xK3w0TIaJ3ll4POWpWUYaA2bRjGGujGT8w" crossorigin="anonymous" referrerpolicy="origin">


</head>
<body scroll="none" >
<div class=container>

	<?php if (sizeof($result) > 0) {?>
	<div class="not-check-all"   >&nbsp; 
	  <a href="#" class="a-plain" onclick="ack('all')"><i class="fas fa-trash" aria-hidden="true"></i> Todos </a>
	</div>
	<?php } else { ?>
	<div class="not-check-all-reload"   >&nbsp;
		<a href="#" class="a-plain" onclick="document.location.reload()"><i class="fas fa-sync-alt" aria-hidden="true"></i> Actualizar</a>
	</div>	
	<?php }?>

	<h2> Notificaciones</h2>

	<div class="not-list-container">


	<?php if (sizeof($result) > 0) {?>
	<div class="not-list">
		<?php
		foreach ($result as $key => $row) {
		if(strlen($row) > 0){
		$d = json_decode($row);
		$s = property_exists($d, 'ts') ? $d->ts :  "";
		$t = property_exists($d, 'tit') ? $d->tit :  ".";
		$m = property_exists($d, 'msg') ? $d->msg :  "&nbsp;";
		$y = property_exists($d, 'type') ? $d->type :  0;
		$img = 'fa-check';
		if($y == 1){ $img = 'fa-rocket'; 			 }
		if($y == 2){ $img = 'fa-calendar-alt';  				 }
		if($y == 3){ $img = 'fa-exclamation-triangle';   }
		if($y == 4){ $img = 'fa-shopping-cart';  		 }

		?>
		<div class="not-row" id="msg_<?php echo $key ?>" >
			<div class="not-item-icon"><i class="fas <?php echo $img ?>" aria-hidden="true"></i></div>
			<div class="not-item-center" >
				<div class="not-item-center-tit"><span  alt="<?php echo $s ?>"> <?php echo $t ?></span></div>
				<div class="not-item-center-msg"><?php echo $m ?></div>
			</div>
			<div class="not-item-actions" >
			<a href="#" onclick="ack('<?php echo $key ?>')" class="i-button" ><i class="fas fa-trash" aria-hidden="true"></i></a>
			</div>
		</div>
		<?php } } ?>
	</div>
	<?php } else { ?>
	<div class="not-list-empty">
		<div class="not-row-empty"><i class="fas fa-inbox" aria-hidden="true"></i> No hay mensajes</div>
	</div>
	<?php } ?>
	</div>
</div>
</body>
<script type="text/javascript">
	
var customerId = "<?php echo $customer_id ?>";

function ack(id) {
	$.ajax({
	  url: "/notification/ack/"+customerId+"/"+id,
	}).done( function(){
		if( id == 'all') {
			console.log('reload');
			document.location.reload();
		} else {
			console.log('hide');
		    $('#msg_'+id).remove(); 	
		}
	});
}



</script>
</html>
