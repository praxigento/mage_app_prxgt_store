WIO_MassactionForm =
{
	idForm: 'wio-mass-action',
	classCheckbox: 'wio-mass-action-checkbox',
	confirmSubmit: false,
	hiddenName: 'wio-action-element[]',
	baseUrl:'',
	submitForm: false,

	_submit: function(_confirm_text, _select_some_text, _select_action_text)
	{
		var els = this.prepareElements();
		if(!els.length)
		{
			alert(_select_some_text);
			return false;
		}
		var form = $(this.idForm);
		var action = form.getAttribute('action');
		if(action == 'no_action' || form.getAttribute('no_action') == 'true')
		{
			alert(_select_action_text);
			return false;
		}
		if(this.confirmSubmit)
		{
			if(confirm(_confirm_text))
			{
				this._processSubmit(form, els);
			}
			else
			{
				return false;
			}
		}
	},

	prepareElements: function()
	{
		var els  = [];
		var _els =  $$('input.' + this.classCheckbox);
		for(var a=0; _els.length > a; a++)
		{
			if(_els[a].tagName)
			{
				if(_els[a].checked)
				{
					els.push(_els[a])
				}
			}
		}
		return els;
	},

	_processSubmit: function(_form, _els)
	{
		for(var a=0; _els.length > a; a++)
		{
			this._addHidden(_form, _els[a].value);
		}
		if(this.submitForm)
		{        	_form.submit();
		}
	},

	_addHidden: function(_form, _value)
	{
		_form.innerHTML += '<input type="hidden" name="'+this.hiddenName+'" value="'+_value+'" />';
	},

	_onchange: function(_el, selectedIndex)
	{
		if(_el.tagName.toLowerCase() == 'a' && !selectedIndex)
		{        	var action = _el.getAttribute('href_a');
		}
		else
		{
			var el      = _el.options[selectedIndex];
			var confirm = el.getAttribute('confirm');
			this.confirmSubmit = confirm;
			var action = el.getAttribute('value');
			action = this.baseUrl + action;
		}
		var form   = $(this.idForm);
		if(form)
		{
			form.setAttribute('action', action);
			if(action == 'no_action')
			{
				form.setAttribute('no_action', true);
			}
			else
			{
				form.setAttribute('no_action', false);
			}
		}
	},

	_selectAll: function(_select)
	{
		var al = $$('input.' + this.classCheckbox);
		for(var a=0; al.length > a; a++)
		{
			var el = al[a];
			if(el.tagName)
			{
				el.checked = _select;
			}
		}
	}
}