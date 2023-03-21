<?php 
/*
    Template Name:Для участников
*/

    get_header();
?>

   <main class="for-participants">
        <section class="header">
            <div class="container">
                <div class="breadcrumbs">
                    <a href="/">Главная</a> / <a href="#">Информация для участников</a>
                </div>
                <h1>ИНФОРМАЦИЯ ДЛЯ УЧАСТНИКОВ</h1>
            </div>
        </section>

        <section class="table">
            <div class="container">
				<button class=" get-more yellow">
					Обновить данные
				</button>
				<p class="update-complete">
					
				</p>
				<?php $posts = get_posts(array('post_type'=>'lot_table','numberposts'=>1));?>		
				<?php 
					$lot_table = $posts[0]; 
					$id = $lot_table->ID;
					$table = get_field('table_for_participants',$id);
				?>
                <table class="lots">
					<thead>
						<tr>
							<th>Число</th>
							<th>МAX(да)</th>
							<th>Сейчас(да)</th>
							<th>MAX(нет)</th>
							<th>Сейчас(нет)</th>
						</tr>
					</thead>
					<tbody>
						
                	</tbody>
                </table>
            </div>
        </section>
        <section class="analytics">
            <div class="container">
                <h2>АНАЛИТИКА И СТАТИСТИКА</h2>

                <div class="image-holder">
                    <img src="/wp-content/themes/lot/assets/images/graph.svg">
                </div>
            </div>
        </section>

        <section class="attention-text">
            <div class="container">
                <p>Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                    Ни в коем случае не играйте на последние деньги в семье и тем более не делайте ставки на деньги,
                    взятые в долг!
                </p>
            </div>
        </section>

        <?php get_template_part('template-parts/footer-banner');?>
    </main>

<?php 
    get_footer();
?>