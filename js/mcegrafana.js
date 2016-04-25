(function() {
    tinymce.PluginManager.add('grafana', function(editor, url) {
            editor.addButton('grafana', {
                //text : 'Grafana',
                image: url + '/image.png',
                onClick : function() {
                	var gr_db_list_array = [];
                	gr_db_list.forEach(function(item, index){
                		gr_db_list_array.push({text:item.title,value:item.uri});                		
                	});
                	
                	editor.windowManager.open({
                		title: 'Grafana Plugin',
                		body: [
                		  {
                			  type: 'listbox',
                			  name: 'dashboards',
                			  label: 'Dashboard to use',
                			  values: gr_db_list_array, //[{text:'watchtower-1', value:'watchtower-1'},
                			           //{text:'watchtower-2', value:'watchtower-2'},
                			           //{text:'weather-1', value: 'weather-1'},
                			           //{text: 'weather-2', value:'weather-2'}],
                			  minWidth: 800
                		  },
                 		  {
                 			  type: 'listbox',
                 			  name: 'panel',
                 			  label: 'Panel',
                 			  values: [],
                 			  minWidth: 100
                 		  }
        		       ],
        		       onsubmit: function(e) {
        		    	   editor.insertContent('[grafana_chart dashboard='+e.data.dashboard+' panel='+e.data.panel+']');
        		       }
                	});
                	var dashboards = document.getElementById('dashboards');
                	dashboards.addEventListener("change", function(){
                		var currentdash = dashboards.options[dashboards.selectedIndex].value;
                		var panelTags = '';
                		gr_panel_list[currentdash].forEach(function(item, index){
                			panelTags += '<option value=x>text</option>';
                		});
                		document.getElementById('panel').innerHtml = panelTags;
                		
                		
                		
                	});

                }})
            });
    })();