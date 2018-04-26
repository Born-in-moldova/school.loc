(function( $ ) { //dataTables filters

    var dtFilters = $.fn.dtFilters = function(options) {
        var returnObj = {};

        var _dtFilters = {}; //main filter collector

        var _selector = this.selector;

        var _containerSelector = this.container;

        var _dtFilterSettings = { //default config
            'filterContainer' : '',
            'container': '.dt-filter-list', //active filter container
            'ulClass' : 'filter-plugin-list',
            'liClass' : 'filter-plugin-element',
            'debug' : false,
            'autoApply' : true, //apply filter after each setting, if false will be applied by button
            callBack: function(){ //main callback
            },
            onSet: function(callerObj, filterObj){ //callback on setting a filter
                //callerObj - object which called the setting operation
                //filterObj - object which describes the activated filter
            },
            onDelete: function(filterObj){ //callback on deleting a filter
                //filterObj - object which describes the deactivated filter
            },
            onApply: function(callerObj){
                //callerObj - object which called the setting operation
            },
            onReset: function(callerObj){
                //callerObj - object which called the setting operation
            }
        }

        var methods = {
            //(public) Initialization the filter list
            init: function(){
                methods.createUl();

                this.each(function(){

                    if (typeof _dtFilters[this.name]  != 'object' ){
                        methods.addFilter(this);
                    }
                })

                methods.controlButtons();

                methods.addFilterListeners();

            },
            reInit: function(){
                delete _dtFilters;
                _dtFilters = {};

                $(_selector).each(function(){

                    if (typeof _dtFilters[this.name]  != 'object' && $(this).parents(_dtFilterSettings.container).length){
                        methods.addFilter(this);
                    }
                })
                if(_dtFilterSettings.debug){
                    console.log(_dtFilters);
                }
            },
            //(private) Create UL as list for filters
            createUl: function(){
                if(!$(_dtFilterSettings.container).length)
                throw new Error('Container should be set in settings!');

                $container = $(_dtFilterSettings.container);
                $container.html("<ul class='" + _dtFilterSettings.ulClass + "'></ul>");
            },
            //(private) Add a item(li) on filter list(ul)
            activateFilter: function(filter){
                if(filter.currentValue.value == filter.defaultValue.value)
                return false;

                $ulFilters = $(_dtFilterSettings.container + " ." + _dtFilterSettings.ulClass);
                if(!$ulFilters.find('li.af-'+filter.name).length){
                    liHTML = '<li onclick="" class="af-' + filter.name + '"><span>' + filter.filterLabel + ': <strong>' + filter.currentValue.text + '</strong> </span> <a class="dt-filter-delete fa fa-times" data-parent="' + filter.name + '">X</a></li>';
                    $ulFilters.append(liHTML);
                }else{
                    $(_dtFilterSettings.container + ' li.af-'+filter.name + " strong").text(filter.currentValue.text);
                }
            },
            //(private) Remove an item(li) from filter list(ul)
            deactivateFilter: function(filter){
                if($(_dtFilterSettings.container + ' li.af-'+filter.name).length)
                $(_dtFilterSettings.container + ' li.af-'+filter.name).remove();
            },
            //(private) Buttons 'Reset' and 'Apply'
            controlButtons: function(){
                $ul = $(_dtFilterSettings.container + " ." + _dtFilterSettings.ulClass);
                if(!$ul.children('li').length){
                    $ul.siblings().remove();
                    return false;
                }

                if(!_dtFilterSettings.autoApply){
                    if(!$(_dtFilterSettings.filterContainer).find('.dt-filter-apply').length && !$ul.siblings('.dt-filter-apply').length){
                        aApply = '<a class="dt-filter-apply dt-filter-apply-buttons">Apply</a>';
                        $ul.parent().append(aApply);
                    }
                }
				
				if(!$(_dtFilterSettings.filterContainer).find('.dt-filter-reset').length && !$ul.siblings('.dt-filter-reset').length){
					aReset = '<a class="dt-filter-reset dt-filter-reset-buttons"></a>';
					$ul.parent().prepend(aReset);
				}
            },
            //(private) Append class to buttons
            addClassToButtons: function(buttonClass, className){
                $(_dtFilterSettings.filterContainer + ' .' + buttonClass).addClass(className);
            },
            //(private) Append class to buttons
            removeClassFromButtons: function(buttonClass, className){
                $(_dtFilterSettings.filterContainer + ' .' + buttonClass).removeClass(className);
            },
            //(private) Add a filter to the filter collector
            addFilter: function(obj){
                var idFilter;
                if(obj.name != "" && obj.name != undefined)
                idFilter = obj.name;
                else if($(obj).attr('data-name') != "")
                idFilter = $(obj).attr('data-name');
                else
                throw new Error('Object should have "name" or "data-name" attribute');

                if(typeof _dtFilters[idFilter] == 'object')
                return false;

                _dtFilters[idFilter] = {};
                _dtFilters[idFilter].jqObj = $(obj);

                if(obj.name)
                _dtFilters[idFilter].name = obj.name;
                else
                _dtFilters[idFilter].name = _dtFilters[idFilter].jqObj.attr('data-name');

                _dtFilters[idFilter].filterLabel = _dtFilters[idFilter].jqObj.attr('data-title');
                _dtFilters[idFilter].tagName = obj.tagName;

                switch(obj.tagName){
                    case 'INPUT':
                    _dtFilters[idFilter].type = _dtFilters[idFilter].jqObj.prop('type');
                    break;
                    case 'SELECT':
                    _dtFilters[idFilter].type = 'select';
                    break;
                    case 'A':
                    _dtFilters[idFilter].type = 'button';
                    break;
                }

                _dtFilters[idFilter].defaultValue =  methods.getFilterDefaultVal(_dtFilters[idFilter]);;
                _dtFilters[idFilter].currentValue =  methods.getFilterVal(_dtFilters[idFilter]);
                methods.activateFilter(_dtFilters[idFilter]);
            },
            //(public) Remove an active filter
            removeFilter: function(name){
                methods.toDefault(_dtFilters[name]);
                methods.deactivateFilter(_dtFilters[name]);
                methods.callCallback();
            },
            //(private) Remove one or all filters from
            clearActiveFilters: function(filterName){
                var activeFilters = {};

                filterName ? activeFilters[filterName] = _dtFilters[filterName] : activeFilters = methods.getActiveFilters()

                for(var obj in activeFilters){
                    methods.deactivateFilter(activeFilters[obj]);
                    methods.toDefault(activeFilters[obj]);

                    _dtFilterSettings.onDelete(methods.getFilter(activeFilters[obj]));
                }

                (_dtFilterSettings.autoApply || !$(_dtFilterSettings.container + " ." + _dtFilterSettings.ulClass).children('li').length) && methods.callCallback(true);

                methods.removeClassFromButtons('dt-filter-apply-buttons','active');

                methods.controlButtons();
            },
            //(private) Handler with value of the filter on setting operation
            proccessingFilter: function(obj, new_value, value_text){

                if(obj.name != "" && obj.name != undefined)
                idFilter = obj.name;
                else if($(obj).attr('data-name') != "")
                idFilter = $(obj).attr('data-name');
                else
                throw new Error('Object should have "name" or "data-name" attribute');

                if(_dtFilters[idFilter]){

                    var filter = _dtFilters[idFilter];

                    if(filter.currentValue.value == new_value)
                    return false;

                    methods.updateFilter(filter, new_value, value_text);

                    filter.currentValue.text = value_text;
                    filter.currentValue.value = new_value;

                    if(filter.currentValue.value == filter.defaultValue.value/* || filter.currentValue.value == ""*/){
                        methods.deactivateFilter(filter);

                        (!$(_dtFilterSettings.container + " ." + _dtFilterSettings.ulClass).children('li').length) && methods.callCallback(true);
                    }else
                    methods.activateFilter(filter);

                }else{
                    methods.addFilter(obj);
                    _dtFilters[idFilter].independent = true;
                }

                methods.callCallback();

                methods.removeClassFromButtons('dt-filter-apply-buttons','active');

                _dtFilterSettings.onSet(obj, methods.getFilter(_dtFilters[idFilter]));

                methods.controlButtons();
            },
            //(private) Handler with value of the filter on updating operation
            updateFilter: function(filter, new_value, value_text){
                if(filter.currentValue.value == new_value)
                return false;

                switch(filter.type){
                    case 'select':
                    filter.currentValue.value = new_value;

                    if(filter.jqObj.attr('multiple') != undefined){
                        values = new_value.split(',');
                        var texts = [];

                        for(var v in values){
                            filter.jqObj.children('option[value="' + values[v] +'"]').attr('selected', 'selected');
                            texts.push(filter.jqObj.children('option[value="' + values[v] +'"]').text());
                        }

                        if(texts.length)
                        filter.currentValue.text = texts.join(',');
                        else
                        filter.currentValue.value = new_text;
                    }else{
                        var $selectedOption = filter.jqObj.children('option[value="' + filter.currentValue.value +'"]');
                        $selectedOption.attr('selected','selected');
                        filter.currentValue.text = $selectedOption.text();
                    }
                    break;
                    case 'radio':
                    //$('input[name=' + filter.name + '][type=radio][value="' + filter.currentValue.value  + '"]' + _selector + '').removeAttr('checked');
                    filter.currentValue.value = new_value;
                    var $checkedRadio = $(_dtFilterSettings.filterContainer + ' input[name=' + filter.name + '][type=radio][value="' + filter.currentValue.value + '"]' + _selector);
                    $checkedRadio.attr('checked','checked');

                    if($checkedRadio.attr('data-value-text') == undefined)
                    throw new Error('Radio should have attribute "data-value-text" ');

                    filter.currentValue.text = $checkedRadio.attr('data-value-text');
                    break;
                    case 'text':
                    case 'number':
                    filter.currentValue.value = new_value;
                    filter.jqObj.val(filter.currentValue.value);
                    filter.currentValue.text = new_value;
                    break;
                    case 'checkbox':
                    var values = new_value.split(',');
                    var texts = [];

                    for(var v in values){
                        $(_dtFilterSettings.filterContainer + ' input[name=' + filter.name + '][type=checkbox][value="' + values[v] +'"]').attr('checked', 'checked');
                        texts.push($(_dtFilterSettings.filterContainer + ' input[name=' + filter.name + '][type=checkbox][value="' + values[v] +'"]').data('value-text'));
                    }

                    filter.currentValue.value = new_value;

                    if(texts.length)
                    filter.currentValue.text = texts.join(',');
                    else
                    filter.currentValue.value = new_text;
                    break;
                    case 'button':
                    filter.currentValue.value = new_value;
                    if(value_text == undefined)
                    throw new Error('Button should have attribute "data-value-text" ');

                    filter.currentValue.text = value_text;
                    filter.currentValue.value = new_value;
                    break;
                }
                methods.activateFilter(filter);
            },
            //(private) Set a filter to default value
            toDefault: function(filter){
                switch(filter.type){
                    case 'select':
                    filter.jqObj.children('option[value="' + filter.defaultValue.value +'"]').prop('selected','selected');
                    filter.currentValue = $.extend({},filter.defaultValue);
                    break;
                    case 'radio':
                    $(_dtFilterSettings.filterContainer + ' input[name=' + filter.name + '][type=radio][value="' + filter.defaultValue.value + '"]' + _selector + '').prop('checked','checked');
                    filter.currentValue = $.extend({},filter.defaultValue);
                    break;
                    case 'text':
                    case 'number':
                    filter.jqObj.val(filter.defaultValue.value);
                    filter.currentValue = $.extend({},filter.defaultValue);
                    break;
                    case 'checkbox':
                    $(_dtFilterSettings.filterContainer + ' input[name=' + filter.name + '][type=checkbox]' + _selector + '').removeAttr('checked');
                    filter.currentValue = $.extend({},filter.defaultValue);
                    break;
                    case 'button':
                    if(_dtFilters[filter.name].independent)
                    delete _dtFilters[filter.name];
                    else
                    filter.currentValue = $.extend({},filter.defaultValue);
                    break;
                }
            },
            //(private) Add listeners for all elements
            addFilterListeners: function(){

                methods.addListnersSelect();

                methods.addListnersRadio();

                methods.addListnersText();

                methods.addListnersCheckbox();

                methods.addListnersA();

                methods.addListnersX();

                methods.addListnersReset();

                methods.addListnersApply();

            },
            //(private) Add listener for SELECT
            addListnersSelect: function(){
                $('body').on('change', _dtFilterSettings.filterContainer + ' select' + _selector, function(){
                    var new_value;
                    var new_text;

                    if($(this).attr('multiple') != undefined){
                        var texts = [];
                        var values = [];

                        $('option:selected', this).each(function(){
                            var val_text = $(this).data('value-text');

                            if(val_text == undefined)
                            val_text = this.text;

                            texts.push(val_text);
                            values.push(this.value);
                        });
                        new_text = texts.join(', ');
                        new_value = values.join(',');
                    }else{
                        var val_text = $('option:selected', this).data('value-text');

                        if(val_text == undefined)
                        val_text = $('option:selected', this).text();

                        new_text = val_text;
                        new_value = $(this).val();
                    }
                    methods.proccessingFilter(this, new_value, new_text);
                })
            },
            //(private) Add listener for ETXT
            addListnersText: function(){
                $('body').on('change', _dtFilterSettings.filterContainer + ' input[type=text]' + _selector, function(e){
                   
                    var new_value = $(this).val();
                    var new_text = $(this).val();
                    methods.proccessingFilter(this, new_value, new_text);
                });
                $('body').on('change',_dtFilterSettings.filterContainer + ' input[type=number]' + _selector, function(e){
                    var new_value = $(this).val();
                    var new_text = $(this).val();
                    methods.proccessingFilter(this, new_value, new_text);
                });
            },
            //(private) Add listener for CHECKBOX
            addListnersCheckbox: function(){
                $('body').on('click', _dtFilterSettings.filterContainer + ' input[type=checkbox]' + _selector, function(){
                    var new_value, new_text;
                    var jqObj = $(this);
                    var texts = [];
                    var values = [];

                    $('input[type=checkbox][name=' + this.name + ']:checked').each(function(){
                        $this = $(this);

                        if($this.attr('data-value-text') == undefined)
                        throw new Error('Checkbox should have attribute "data-value-text"');

                        texts.push($this.attr('data-value-text'));
                        values.push($this.attr('value'));
                    });

                    new_text = texts.join(', ');
                    new_value = values.join(',');

                    methods.proccessingFilter(this, new_value, new_text);

                })
            },
            //(private) Add listener for RADIO
            addListnersRadio: function(){
                $('body').on('click', _dtFilterSettings.filterContainer + ' input[type=radio]' + _selector, function(){
                    var jqObj = $(this);

                    if(jqObj.attr('data-value-text') == undefined)
                    throw new Error('Radio should have attribute "data-value-text"');

                    new_text = jqObj.attr('data-value-text');
                    new_value = jqObj.val();

                    methods.proccessingFilter(this, new_value, new_text);
                })
            },
            //(private) Add listener for A(ADD BUTTONS)
            addListnersA: function(){
                $('body').on('click', _dtFilterSettings.filterContainer + " a" + _selector, function(e){
                    e.preventDefault();

                    var jqObj = $(this);

                    if(jqObj.attr('data-name') == undefined)
                    throw new Error('A should have "data-name" attribute');

                    if(jqObj.attr('data-value') == undefined)
                    throw new Error('A should have "data-value" attribute');

                    new_value = jqObj.attr('data-value');
                    new_text = jqObj.attr('data-value-text');

                    methods.proccessingFilter(this, new_value, new_text);
                });
            },
            //(private) Add listener for X(REMOVE BUTTONS)
            addListnersX: function(){
                $('body').on('click', _dtFilterSettings.filterContainer + " " + _dtFilterSettings.container + " .dt-filter-delete", function(){
                    var filterId = $(this).attr('data-parent');
                    methods.clearActiveFilters(filterId);
                    //methods.callCallback();
                });
            },
            //(private) Add listener for RESET BUTTON
            addListnersReset: function(){
                $('body').on('click', _dtFilterSettings.container + " .dt-filter-reset-buttons", function(){
                    methods.clearActiveFilters();
                    //methods.callCallback(true);
                    _dtFilterSettings.onReset();
                });
            },
            //(private) Add listener for APPLY BUTTON
            addListnersApply: function(){
                $('body').on('click', _dtFilterSettings.filterContainer + " .dt-filter-apply-buttons", function(){
                    if($(this).hasClass('active'))
                    return false;

                    methods.callCallback(true);
                    _dtFilterSettings.onApply();
                    methods.addClassToButtons('dt-filter-apply-buttons','active');
                });
            },
            //(private) Get current value of the filter
            getFilterVal: function(filter){
                var filterValue = {};

                switch(filter.type){
                    case 'select':
                    if(filter.jqObj.attr('multiple') != undefined){
                        var texts = [];
                        var values = [];
                        filter.jqObj.children(_selector + ' option:selected').each(function(){
                            var val_text = $(this).data('value-text');

                            if(val_text == undefined)
                            val_text = this.text;

                            texts.push(val_text);
                            values.push(this.value);
                        });
                        new_text = texts.join(', ');
                        new_value = values.join(',');
                    } else {
                        var $optionSelected = filter.jqObj.children(_selector + ' option:selected').first();
                        var val_text = $optionSelected.data('value-text');

                        if(val_text == undefined)
                        val_text = $optionSelected.text();

                        new_text = val_text;
                        new_value = $optionSelected.val();
                    }
                    filterValue.text = new_text;
                    filterValue.value = new_value;

                    break;
                    case 'radio':
                    var $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + '][data-current=true]' + _selector);

                    if(!$radioChecked.length)
                    $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + ']:checked' + _selector);

                    if(!$radioChecked.length)
                    $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + '][data-default=true]' + _selector);

                    if(!$radioChecked.length)
                    $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + '][value=""]' + _selector);

                    if($radioChecked.length)
                    $radioChecked.attr('checked', 'checked');

                    filterValue.text = $radioChecked.attr('data-value-text');
                    filterValue.value = $radioChecked.val();
                    break;
                    case 'text':
                    case 'number':
                    filterValue.text = filterValue.value = filter.jqObj.val();
                    break;
                    case 'checkbox':
                    var texts = [];
                    var values = [];
                    $(_dtFilterSettings.filterContainer + ' input[name="' + filter.name + '"]:checked' + _selector).each(function(){
                        texts.push($(this).attr('data-value-text'));
                        values.push($(this).attr('data-value'));
                    });

                    filterValue.text = texts.length ? texts.join(', ') : "";
                    filterValue.value = values.length ? values.join(',') : "";
                    break;
                    case 'button':
                    var $activeButton = $(_dtFilterSettings.filterContainer + ' a[data-name=' + filter.name + '][data-current=true]' + _selector);

                    if(!$activeButton.length)
                    $activeButton = filter.jqObj;

                    filterValue.text = $activeButton.attr('data-value-text');
                    filterValue.value = $activeButton.attr('data-value');
                    break;
                }
                return filterValue;
            },
            //(private) Get default value of the filter
            getFilterDefaultVal: function(filter){
                var filterValue = {};

                switch(filter.type){
                    case 'select':
                    var $optionsSelected = filter.jqObj.children('option[data-default=true]');

                    if(filter.jqObj.attr('multiple') != undefined){
                        if(!$optionsSelected.length){
                            filterValue.text = "";
                            filterValue.value = "";
                        }else{
                            var texts = [];
                            var values = [];
                            $optionsSelected.each(function(){
                                var val_text = $(this).data('value-text');

                                if(val_text == undefined)
                                val_text = this.text;

                                texts.push(val_text);
                                values.push(this.value);
                            });
                            filterValue.text = texts.join(', ');
                            filterValue.value = values.join(',');
                        }
                    }else{
                        var $optionSelected = $optionsSelected.first();

                        if(!$optionSelected.length)
                        $optionSelected = filter.jqObj.children('option[value=""]').first();

                        if(!$optionSelected.length){
                            filterValue.text = "";
                            filterValue.value = "";
                        }else{
                            var val_text = $optionSelected.data('value-text');

                            if(val_text == undefined)
                            val_text = $optionSelected.text();

                            filterValue.text = val_text;
                            filterValue.value = $optionSelected.val();
                        }
                    }
                    break;
                    case 'radio':
                    var $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + '][data-default=true]' + _selector);

                    if(!$radioChecked.length)
                    $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + '][value=""]' + _selector).first();

                    if(!$radioChecked.length)
                    $radioChecked = $(_dtFilterSettings.filterContainer + ' input[type=radio][name=' + filter.name + ']' + _selector).first();


                    filterValue.text = $radioChecked.attr('data-value-text');
                    filterValue.value = $radioChecked.val();
                    break;
                    case 'text':
                    case 'number':
                    filterValue.text = filterValue.value = "";
                    break;
                    case 'checkbox':
                    filterValue.text = "";
                    filterValue.value = "";
                    break;
                    case 'button':
                    var $button = $(_dtFilterSettings.filterContainer + ' a[data-name=' + filter.name + '][data-default=true]' + _selector);

                    if(!$button.length)
                    $button = $(_dtFilterSettings.filterContainer + ' a[data-name=' + filter.name + '][data-value=""]' + _selector);

                    if($button.length){
                        filterValue.text = $button.attr('data-value-text');
                        filterValue.value = $button.attr('data-value');
                    }else{
                        filterValue.text = "";
                        filterValue.value = "";
                    }
                    break;
                }
                return filterValue;
            },
            //(private) Get a public info for the filter
            getFilter: function(filter){
                if(filter == undefined)
                return {};

                return {
                    'name'    : filter.name,
                    'label'    : filter.filterLabel,
                    'tag'     : filter.tagName,
                    'value'    : filter.currentValue.value,
                    'default': filter.defaultValue.value
                };
            },
            //(private) Get list of the active filters
            getActiveFilters: function(){
                var returnObj = {};
                for(var obj in _dtFilters){
                    if(_dtFilters[obj].currentValue.value != _dtFilters[obj].defaultValue.value)
                    returnObj[_dtFilters[obj].name] = _dtFilters[obj];
                }
                return returnObj;
            },
            //(public) Get list of the active filters in format for DataTables
            getFilterDTFormat: function(){
                var returnArray = [];
                var activeFilters = methods.getActiveFilters();

                for(var key in activeFilters)
                returnArray.push({"name":activeFilters[key].name, "value":activeFilters[key].currentValue.value});

                if(_dtFilterSettings.debug){
                    console.log(_dtFilters);
                    console.log(returnArray);
                }

                return returnArray;
            },
            //(private) Caller of main callback
            callCallback: function(forced){
                (forced || _dtFilterSettings.autoApply) && _dtFilterSettings.callBack();
            }
        };

        //Begin
        _dtFilterSettings = $.extend({}, _dtFilterSettings, options);
        
        methods.init.apply( this, arguments );

        returnObj.getDTFilter    = methods.getFilterDTFormat;
        returnObj.removeFilter     = methods.removeFilter;
        returnObj.reInit         = methods.reInit;
        return returnObj;
    }


})(jQuery);
