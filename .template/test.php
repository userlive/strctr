<?php
	function parse($v) {

		switch(gettype($v)) {
			case "boolean":
				$v = $v ? "true" : "false";
				break;
			case "integer":

				break;
			case "double":

				break;
			case "string":
				$l = strlen($v);
				$v = $l > 128 ? substr($v, 0, 128) : $v;
				break;
			case "array":
					$v = 'array';
				break;
			case "object":
				$v = get_class($v);
				break;
			case "resource":

				break;
			case "resource (closed)":

				break;
			case "NULL":
				$v = "NULL";
				break;
			case "unknown type":

				break;
		}

		return $v;

	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="utf-8">
		<title>Тестконтроль</title>
		<style>
			@import url('/.template/test/static/css/base.css');
			@import url('/.template/test/static/css/btn.css');		/* buttons */
			@import url('/.template/test/static/css/acc.css');		/* accordion collapse/expand */
		</style>
		<script src="jq.js"></script>
	</head>
	<body>
		<div class="wrp">

			<h1>Тестовая страница для <?=$object?></h1>

			<h2>Испытание HTTP методов</h2>
				<p class="methods">
					<a href="#" class="click-btn method" data-tab="1"><span>OPTIONS</span></a>
					<a href="#" class="click-btn active method" data-tab="2"><span>GET</span></a>
					<a href="#" class="click-btn method" data-tab="3"><span>HEAD</span></a>
					<a href="#" class="click-btn method" data-tab="4"><span>POST</span></a>
					<a href="#" class="click-btn method" data-tab="5"><span>PUT</span></a>
					<a href="#" class="click-btn method" data-tab="6"><span>PATCH</span></a>
					<a href="#" class="click-btn method" data-tab="7"><span>DELETE</span></a>
					<a href="#" class="click-btn method" data-tab="8"><span>TRACE</span></a>
					<a href="#" class="click-btn method" data-tab="9"><span>CONNECT</span></a>
					<a href="#" class="method"> </a>
				</p>

				<div data-tab="1" class="interpret">
					<h4><b>OPTIONS</b> — </h4>
					<p>Получить доступные методы и поддерживаемые расширения. Запрос к объекту вернет только заголовки. В теле возможно, например, отправлять ключ авторизации.</p>
				</div>
				<div data-tab="2" class="interpret active">
					<h4><b>GET</b> — запрос содержимого ресурса</h4>
					<p></p>
				</div>
				<div data-tab="3" class="interpret">
					<h4><b>HEAD</b></h4>
					<p>тоже самое что и GET, но в ответе нет тела. Запрос к объекту вернёт актуальное состояние заголовков.</p>
				</div>
				<div data-tab="4" class="interpret">
					<h4><b>POST</b></h4>
					<p>создаст новый ресурс или обновит существующий, загрузит файлы. Ответ возвращает разные результаты. Метод не безопасен, входящая информация подлежит контролю на корректность. Рекомендуется к применению в публичных условиях предоставления доступа.</p>
				</div>
				<div data-tab="5" class="interpret">
					<h4><b>PUT</b></h4>
					<p>аналогичен методу POST. Метод указывает что заголовки запросу подлежат контролю на корректность. Рекомендуется к применению в условиях авторизованного доступа.</p>
				</div>
				<div data-tab="6" class="interpret">
					<h4><b>PATCH</b></h4>
					<p>аналогичен методу PUT. Подразумевается запрос к уже существующему объекту для обновления значения конкретного атрибута.</p>
				</div>
				<div data-tab="7" class="interpret">
					<h4><b>DELETE</b></h4>
					<p>удалить указанный объект.</p>
				</div>
				<div data-tab="8" class="interpret">
					<h4><b>TRACE</b></h4>
					<p>вернет клиенту историю преобразования заголовков запроса переданного через промежуточный сервер.</p>
				</div>
				<div data-tab="9" class="interpret">
					<h4><b>CONNECT</b> — установка ssl соединения.</h4>
					<p>установка ssl соединения.</p>
				</div>



			<!--div class="control">
				<p>Тип доступа - авторизованный, публичный, роль: client, admin<br />
				Авторизованный доступ — информация об авторизации<br />
				Родитель<br />
				Уровень абстракции<br />
				Системные роли:
				<ul>
					<li>Superuser all api</li>
					<li>Programmist, api: code, structure, templates, debug, test, analitics, users</li>
					<li>Administrator, api: nginx or apache, postfix, php-fpm, analitics, users</li>
					<li>...</li>
				</ul>
				Системная информация: кэш, бд<br /></p>
			</div-->
<?php

$report = [
	[apache_request_headers(), 	$config, 			$this->status, 		$strctr, 		$_SESSION, 			apache_response_headers()],
	['Заголовки запроса',		'Конфигурация', 	'Статус объекта', 	'Атрибуты', 	'Профиль сессии', 	'Заголовки ответа'],
	['request',					'config', 			'status', 			'strctr', 		'session', 			'response']
];
?>
			<h2>Отчет исполнения</h2>
			<div class="cols report">
<?php foreach($report[0] as $index => $property) {
		$name = 'Свойство';
		$class = 'property';
		$name = array_key_exists($index, $report[1]) ? $report[1][$index] : $name;
		$class = array_key_exists($index, $report[2]) ? $report[2][$index] : $class;

?>
				<div class="col <?=$class?>">
					<h4><?=$name?></h4>
					<ul>
<?php 	foreach($property as $name => $value) { ?>
						<li>
							<span class="name"><b><?=$name?></b></span> <span class="value" contenteditable="true"><?=var_export($value, true)?></span>
						</li>
<?php 	} ?>
					</ul>
				</div>
<?php } ?>
			</div>

			<!--center>
				<a href="#" class="btn">↓ Актуальное состояние объекта</a>
			</center-->

	<div class="spoiler" data-target="1">
		<div class="content">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Ipsum, reiciendis!</div>
	</div>

		</div>

		<script>
			$(function(){
				$('#tableTest').submit(function(e){
					e.preventDefault();
					alert(1);
				});

				jQuery('a').on('click', function (e) {

					console.log(111);
					e.preventDefault();

				});

				$('.method').click(function() {
					var id = $(this).attr('data-tab'),
					   content = $('.interpret[data-tab="'+ id +'"]');

					$('.method.active').removeClass('active');
					$(this).addClass('active');

					$('.interpret.active').removeClass('active');
					content.addClass('active');
				});

			});
		</script>
		<!--pre>
	<?php var_dump(); ?>
	<?php var_dump(headers_list()); ?>
	<?php var_dump(headers_sent()); ?>
		</pre-->
	</body>
</html>
