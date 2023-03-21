<?php 
    /*
        Template Name: Личные данные
    */
    get_header();
    if(!is_user_logged_in()) { 
		header('location: /login');
	}
?>



<main class="account-information">
	<div class="container">
			<h1 class="account-header">
				ЛИЧНЫЙ КАБИНЕТ
			</h1>
	<form class="form account-form account__wrap">
		
    <?php 
		$user = wp_get_current_user();
		$user_meta = get_user_meta($user->ID);
	?> 
		
		<input type="hidden" name="user_id" value="<?=$user->ID?>">
		<div class="account__info">
			<label for="first_name">Имя:</label>
			<?php if(isset($user_meta['first_name'])) {?>
				<input type="text" class="blue" name="first_name" value="<?=$user_meta['first_name'][0]?>">
			<?php } else{ ?>
			 	<input type="text" class="blue" name="first_name" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="last_name">Фамилия:</label>
			
			<?php if(isset($user_meta['last_name'])) {?>
				<input type="text" class="blue" name="last_name" value="<?=$user_meta['last_name'][0]?>">
			<?php } else{ ?>
			 	<input type="text" class="blue" name="last_name" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="email">Email:</label>
			<?php if(isset($user->user_email)) {?>
				<input type="text" class="blue" name="email" value="<?=$user->user_email?>">
			<?php } else{ ?>
				<input type="text" class="blue" name="email" value="">
			<?php }?>
		</div>
		
		<div class="account__info">
			<label for="second_name">Отчество:</label>
			<?php if(isset($user_meta['second_name'])) {?>
				<input type="text" class="blue" name="second_name" value="<?=$user_meta['second_name'][0]?>">
			<?php } else {?>
				<input type="text" class="blue" name="second_name" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="gender">Пол:</label>
			<?php if(isset($user_meta['gender'])) {?>
				<input type="text" class="blue" name="gender" value="<?=$user_meta['gender'][0]?>">
			<?php } else {?>
				<input type="text" class="blue" name="gender" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="last_name">Телефон:</label>
				<?php  if(isset($user_meta['phone'])) {?>
			<input type="text" class="blue" name="phone" value="<?=$user_meta['phone'][0]?>">
			<?php } else{ ?>
				<input type="text" class="blue" name="phone" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="birth">Дата рождения:</label>
			<?php if(isset($user_meta['birth'])) {?>
			<input type="text" class="blue" name="birth" value="<?=$user_meta['birth'][0]?>">
			<?php } else{ ?>
				<input type="text" class="blue" name="birth" value="">
			<?php }?>
		</div>
		<div class="account__info">
			<label for="years_of_playing">Сколько лет играете:</label>
			
			<?php if(isset($user_meta['years_of_playing'])) {?>
			<input type="text" class="blue" name="years_of_playing" value="<?=$user_meta['years_of_playing'][0]?>"> 
			<?php } else{ ?>
			
				<input type="text" class="blue" name="years_of_playing" value=""> 
			<?php }?>
		</div>
		<div class="account__info">
			<button type="submit" class="yellow">
				Обновить
			</button>
			<button class="logout yellow">
				Выйти
			</button>
		</div>
		<p class="update-complete">
			
		</p>
	</form>
		<div class="tariff__buttons">
			<div class="wrapper">
				
			<button class="yellow">
				Тариф 1
			</button>
			<p class="info">
				
			</p>
			</div>
			<div class="wrapper">
				
			<button class="yellow">
				Тариф 2
			</button>
			<p class="info">
				
			</p>
			</div>
			<div class="wrapper">
				
			<button class="yellow">
				Тариф 3
			</button>
			<p class="info">
				
			</p>
			</div>
		</div>
		
	</div>
</main>
<?php 
    get_footer();
?>