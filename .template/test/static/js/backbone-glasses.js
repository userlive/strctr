// table — .template\og\static\snippet\glasses
// new / edit

(function(){

	var def = {
		model : {
			id: null,                                           			// Идентификатор
			preview: null,                                      			// Изображение
			type: 0,                                      					// Солнце защитные очки | оправы | 
			name: "",                                           			// Название складывается из произвольной строки и атрибутов
			brand: "",                                          			// брэнд
			model: "",                                          			// модель объекта
			description: "",												// Произвольный текст
			available: 0,                                       			// Кол-во в наличии
			reserve: 0,														// Кол-во зарезервированного
			sale: 0,														// Кол-во продаж
			price: 0,														// Стоимость
			marge: 0,														// Маржа после закупки, логистики, налогов
			gender: 0,														// 0 - унисекс | 1 - женские | 2 - мужские
			frame_type: null, 	
			frame_form: null, 	
			frame_color: null, 	
			frame_material: null, 	
			filter: 0, 	
			properties: {},	
			files: {},	
			create: '0000-00-00 00:00:00',									// дата создания	
		},

		config: {
			
		},

	}

	OG.glasses = OG.View.extend({
		el: $('#system'),
		templateUrl: "/.template/og/static/snippet/glasses.html",
		tpl: function(){},
		Model: OG.Model.extend({
			defaults: def.model,
			urlRoot: "/og/glasses.json",
			respType: "json",
			initialize: function(def) {
			}
		}),

		Collection: OG.Collection.extend({
			url: "/og/glasses.json",
			model: this.Model,
			count: 0,
			page: 0
		}),

		config: {
			mode: 'table', 								// Тип списка таблица или карточками
			list: 50,									// Кол-во объектов на одной странице
			page: 0,									// Индекс текущей страницы
			item: 0, 									// Индекс текущего объекта для просмотра или редактирования
		},

		state: {
			count: 0,									// Кол-во записей в таблице
			updated: "",								// дата обновления таблицы
			//:"",
			comment: "",								// Комментарий к таблице
		},

		snippet: {
			edit: null,									// Форма для редактирования и создания объектов
			wrap: null,									// Обёртка для списка в таблице
			list: null,									// Элемент списка таблицы
			view: null,									// Предварительный просмотр объекта
			prop: null,									// Свойства объекта
		},

		events: {
			"click .add" : "add",						// Добавить новый
			"click .del" : "del",						// Удалить
			"click .upd" : "upd",						// Обновить существующий
			"click .bye" : "bye",						// Закрыть | отмена просмотра или редактора
		},
		initialize: function(){
			OG.glasses.__super__.initialize.apply(this);
			this.Collection = new this.Collection();
		},
		getForm: function(){
			
			var data = new FormData($("form", this.$el)[0]);

			var add = {};

			data.forEach(function(value, key){
				if(value)
					add[key] = value;
			});
			console.log(add);
			return add;
			
		},
		add: function(){

			var add = this.getForm();
			var fileType = add.preview.type.replace(/\/.+/, '');
			var model = new this.Model(add);

			this.Collection.add([model]);
			var thisview = this;

			model.save().then(function(res){
				var config = {view: "edit", model: res};
				window.location.href = "/#/glasses/item/" + res.id;
			});

		},
		
		upd: function() {
			
		},
		
		bye: function(){
			window.history.back();
		},
		list: function(page = 0) {

			var view = this;

			(async function(){
				
				var glasses = await view.Collection.fetch();
				var listing = "";
				
				_.each(glasses, function(model){
					
					var m = new view.Model(model);
					
					listing += view.snippet.list(m.attributes);
				});

				$(".content", view.$el).html(view.snippet.wrap({
					list: listing
				}));
			})();
			
			return this;
			
		},

		item: function(item = 0) {
			console.log('glasses item');
		},

		edit: function(config) {

			var model = config.model;
			var item = config.item;

			if(this.Collection.length)
				model = this.Collection.get(item);

			if(Number.isInteger(model) && model > 0) {
				model = new this.Model({id: model});
				model.fetch();
				model = model.attributes;
			}

			if(model instanceof OG.Model) {
				model = model.attributes;
			}

			if(!model)
				model = new this.Model().attributes;



			model.title = this.title(model);

			$(".content", this.$el).html(this.snippet.edit(model));

		},
		title: function(attr){
			
			var title = "";
			
			if(attr) {
				switch(attr.type) {
					case "0":
						title = "Солнцезащитные очки";
						break;
					case "1":
						title = "Спортивные очки";
						break;
					case "2":
						title = "Лыжные очки";
						break;
					case "3":
						title = "Оправа";
						break;
					default:
				}
			}
				
			
			return title;
		},
		_render: function(config){

			config = config || {};

			if(!config.view)
				config.view = 'list';

			if(!config.page)
				config.page = 0;

			if(!config.model)
				config.model = new this.Model().attributes;

			var tpl = this.tpl = $("<glasses>" + this.template + "</glasses>");

			this.snippet.wrap = _.template($("#wrap", tpl).html());
			this.snippet.list = _.template($("#list", tpl).html());
			this.snippet.edit = _.template($("#edit", tpl).html());
			this.snippet.view = _.template($("#view", tpl).html());
			this.snippet.prop = _.template($("#prop", tpl).html());

			switch (config.view) {
				case 'edit':
					this.edit(config);
					break;
				default:
					this.list();
			}

			this.config.load = 1

			return this;
		}

	});
	
})();