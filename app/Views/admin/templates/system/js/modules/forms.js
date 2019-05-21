
// A global variable to be used to count the questions and to be put as part of the language tabs
var questionCount = 0;

// A global variable to be used for predefined questions
var readonlyQuestion = false;

/**
 * Adds the header of a question section which will create the start of the languages tabs div
 * @param id
 * @param name
 * @param questionType
 * @param attributes
 * @returns {string}
 */
function addNewQuestionHeader(id, name, questionType, attributes)
{
	// Drawing the tool bar with the move and close buttons
	var header = '<div class="question-section bg-grey-100 col-xs-12 col-sm-12  col-md-12 input-container input-container-input  padding-top-20 margin-top-20">'+
		'<div class="col-xs-12 col-sm-12 col-md-12 padding-0 question-header">'+
		'<i class="fa fa-times-circle fa-arrows-alt move-icon" title="'+translate["sort"]+'"></i>'+
		'<div class="col-xs-12 col-sm-12 col-md-12 padding-0 question-number"><small class="hidden-xs"> - '+translate["question-header"]+'</small></div>'+
		'<i class="fa fa-times-circle remove-question" title="'+translate["remove"]+'"></i>'+
		'</div>';

	// questionIds is the existing question ids
	header += '<div class="col-xs-12 col-sm-12 col-md-12 input-container input-container-select margin-top-20 padding-0"  style="z-index: 100;">' +
		'<input type="hidden" name="questionIds[' +questionCount +']" value="'+id+'">' ;

	// Question name part
	header += addNewQuestionAttribute('text', 'name', '', name) ;

	// Question type part
	header += addQuestionsTypesSelectbox(questionType)+
		'</div>'+

		addNewQuestionRules(attributes, name)+
		// Language tabs
		'<div id="languageTabs" class="col-xs-12 col-sm-12 col-md-12 padding-top-20" >' +
		'<ul class="nav nav-tabs nav-tabs-line " data-plugin="nav-tabs" >';

	var active;

	for (var lang in languages) {
		if(lang == 'en')
			active = 'active';
		else
			active = '';

		header += '<li class="'+active+'" ><a data-toggle="tab" href="#tab-'+lang+ '-' +questionCount +'" >'+languages[lang]+'</a></li>';
	}
	header += '</ul>' +
		'<div class="tab-content clearfix padding-top-20 margin-bottom-20">' ;

	return header;
}

/**
 * Inside each question section there is a dropdownlist that has all question types
 * @param questionType
 * @returns {string}
 */
function addQuestionsTypesSelectbox(questionType)
{
	var typesSelectbox = '';
	if(!readonlyQuestion) {
		typesSelectbox = '<div class="col-xs-12 col-sm-12 col-md-6 ">' +
			'<div class="col-xs-12 col-sm-2 col-md-2 padding-0" >' +
			'<label for="questionTypes'+questionCount+'" class="pull-right margin-right-10 margin-top-10">'+translate["question-types"]+'</label>' +
			'</div>' +
			'<div class="col-xs-12 col-sm-10 col-md-10">' +
			'<div class="form-group ">' +
			'<select class="form-control chosen-select questionTypes" id="questionTypes'+questionCount+'" name="questionTypes['+questionCount+']" title="'+translate["question-types-placeholder"] + '">';

		var selected;

		for (var type in questionTypes) {
			if( type == questionType)
				selected = 'selected';
			else
				selected = '';

			typesSelectbox += '<option value="'+type+'" '+selected+'>'+questionTypes[type]+'</option>' ;
		}
		typesSelectbox += '</select></div></div></div>' ;
	} else {
		typesSelectbox = '<div class="col-xs-12 col-sm-12 col-md-6 ">' +
			'<div class="col-xs-12 col-sm-2 col-md-2 padding-0" >' +
			'<label for="questionTypes' + questionCount + '" class="pull-right margin-right-10">' + translate["question-types"] + '</label>' +
			'</div>' +
			'<div class="col-xs-12 col-sm-10 col-md-10">' +
			'<div class="form-group ">' +
			'<input class="form-control questionTypes" type="text" id="questionTypes' + questionCount + '" name="questionTypes[' + questionCount + ']" title="' + translate["question-types-placeholder"] + '"';

		var selected;

		for (var type in questionTypes) {
			if (type == questionType)
				selected = type;
		}
		typesSelectbox += 'value="' +	selected + '" readonly ></div></div></div>';
	}
	return typesSelectbox;
}

/**
 * Adds the tab content per language
 * @param lang
 * @param attributes
 * @returns {string}
 */
function addQuestionLanguageTab(lang, attributes)
{
	var active = '';
	if(lang == 'en')
		active = 'active';

	var tab = '<div class="tab-pane '+active+' " id="tab-'+lang+ '-' +questionCount +'">'	;

	// Building tab contents
	tab += buildQuestionForm(lang, attributes);
	tab += '</div>';

	return tab ;

}

/**
 * buildQuestionForm will build the form of label and place holder for each language
 * @param lang
 * @param attributes
 * @returns {string}
 */
function buildQuestionForm(lang, attributes)
{
	var labelValue ;
	var placeholderValue;
	var htmlValue = '';

	// Setting up existing question attributes
	if (attributes !== undefined)
	{
		if (attributes['label'] !== undefined)
			labelValue = attributes['label'][lang];

		if (attributes['placeholder'] !== undefined)
			placeholderValue = attributes['placeholder'][lang];

		if (attributes['html'] !== undefined)
			if(attributes['html'][lang] !== undefined)
				htmlValue = attributes['html'][lang];
	}

	var emptyoptionDiv = '<div class="col-xs-12 col-sm-12 col-md-6 question-option padding-bottom-20">' +
		'<div class="col-xs-12 col-sm-2 col-md-2 padding-0 ">' +
		'<label for="question-option'+questionCount + lang+'" class="pull-right margin-right-10 margin-top-10">'+translate["option"]+'</label>' +
		'</div>';

	emptyoptionDiv += '<div class="col-xs-12 col-sm-10 col-md-10  padding-0 question-attribute-option'+lang+'"></div></div>' ;

	var textAreaDiv = '<div class="col-xs-12 col-sm-12 col-md-12 question-html padding-bottom-20">' +
		'<textarea style="width: 100%; min-height: 200px;" name="question-html[' + questionCount + '][' + lang + ']">'+htmlValue+'</textarea>' +
		'</div>';

	///////////
	// Adding label and place holder
	return addNewQuestionAttribute('text','label',lang, labelValue) +
		addNewQuestionAttribute('text','placeholder',lang, placeholderValue) +
		emptyoptionDiv + textAreaDiv;
}

/**
 * Adding a section of the needed question and question attribute
 * @param attributeType
 * @param attributeName
 * @param lang
 * @param value
 * @returns {string}
 */
function addNewQuestionAttribute(attributeType, attributeName, lang, value)
{
	var attribute = '';
	var langArray = '';
	var isDisabled = '';

	if(readonlyQuestion)
		isDisabled = ' DISABLED ';


	if (lang != '')
		langArray = '['+lang+']';

	attribute = '<div class="col-xs-12 col-sm-12 col-md-6 question-'+attributeName+'">' +
		'<div class="col-xs-12 col-sm-2 col-md-2 padding-0 ">' +
		'<label for="question-'+attributeName+questionCount + lang+'" class="pull-right margin-right-10 margin-top-10">'+translate[attributeName]+'</label>' +
		'</div>';

	if(attributeType == 'checkbox')
	{
		attribute += '<div class="col-xs-12 col-sm-10 col-md-10 question-attribute-'+attributeName+lang+'">' ;
		attribute += '<div class="checkbox ">' +
			'<input value="0" type="hidden" name="question-'+attributeName+'['+questionCount+']'+langArray+'" >' +
			'<input value="1" name="question-'+attributeName+'['+questionCount+']'+langArray+'" ';
		if(value == "1")
		{
			attribute += ' CHECKED ';
		}

	}
	else if(attributeName == 'option')
	{
		attribute += '<div class="col-xs-12 col-sm-10 col-md-10 question-attribute-'+attributeName+lang+' padding-0">' ;
		attribute += '<div class="input-group">'  +
			'<input class="form-control optionInput-'+questionCount+'"  ';
	}
	else
	{
		attribute += '<div class="col-xs-12 col-sm-10 col-md-10 question-attribute-'+attributeName+lang+' ">' ;
		attribute += '<div class="form-group ">' +
			'<input class="form-control" name="question-'+attributeName+'['+questionCount+']'+langArray+'" ';

		if(value !== undefined){
			attribute += ' value="' + encodeValue(value) + '"';
		}

	}

	// Disabling the question attributes for predefined questions
	if(lang == '' && readonlyQuestion)
		attribute += ' READONLY ';

	attribute += ' type="'+attributeType+'" id="question-'+attributeName + questionCount+lang +
		'" placeholder="'+translate[attributeName+"-placeholder"]+
		'" title="'+translate[attributeName+"-placeholder"]+'" maxlength="255">' ;

	if(attributeName == 'option')
		attribute += '<span class="input-group-btn">' +
			'<button class="btn btn-info add-value" title="'+translate['add']+'" '+isDisabled+' type="button" data-id="'+questionCount+'"><i class="fa fa-plus fa_circle"></i></button>'+
			'</span>';


	attribute += '</div></div></div>';

	return attribute;
}


/**
 * Will html encode
 * @param value
 * @returns string
 */
function encodeValue(value) {
	return value
		.replace(/&/g, "&amp;")
		.replace(/>/g, "&gt;")
		.replace(/</g, "&lt;")
		.replace(/"/g, "&quot;");
}

/**
 * Closes the question section
 * @returns {string}
 */
function addNewQuestionFooter()
{
	var footer =  '</div></div></div></div>';
	questionCount++;
	return footer;
}

/**
 * Closes the question section
 * @param attributes
 * @returns {string}
 */
function addNewQuestionRules(attributes, name)
{
	var maxlengthValue, minimumValue, defaultValue, maximumValue, optionValues, requiredValue, defaultCheckboxValue, smsValidationCheckboxValue, emailValidationCheckboxValue;

	if(attributes !== undefined)
	{
		maxlengthValue 					= attributes['maxlength'];
		minimumValue 					= attributes['minimum'];
		defaultValue 					= attributes['default'];
		maximumValue 					= attributes['maximum'];
		optionValues 					= attributes['option'];
		requiredValue 					= attributes['required'];
		defaultCheckboxValue 			= attributes['defaultCheckbox'];
		emailValidationCheckboxValue 	= attributes['email-validation'];
		smsValidationCheckboxValue 		= attributes['sms-validation'];
	}

	var rules =  '<div class="col-xs-12 col-sm-12 col-md-12 padding-0" >' +
		addNewQuestionAttribute('text'	,	'option',		'', optionValues);

	if(name !== undefined && name !== 'Mobile Number') {
		rules += addNewQuestionAttribute('number'	,'maxlength',	'', maxlengthValue);
		rules += addNewQuestionAttribute('number'	,'minimum', 	'', minimumValue);
		rules += addNewQuestionAttribute('text'		,'default',		'', defaultValue);

	} else{
		rules += addNewQuestionAttribute('number'	,'minimum', 	'', '11');
	}

	rules += addNewQuestionAttribute('number'	,'maximum',		'', maximumValue)+
		addNewQuestionAttribute('checkbox'	,'defaultCheckbox',		'', defaultCheckboxValue)+
		addNewQuestionAttribute('checkbox'	,'required',	'', requiredValue);

	if(name !== undefined && name === 'Mobile Number')
	{
		// Show the checkbox if we have providers on the Site, or display a message if not
		if(isSmsValidationOn == "1"){
			rules += addNewQuestionAttribute('checkbox', 'sms-validation', '', smsValidationCheckboxValue);
		} else {
			rules += '<p>' + trans['add-sms-creds-to-site-attributes']+'</p>';
		}
	}

	if(name !== undefined && name === 'Email')
		rules += addNewQuestionAttribute('checkbox', 'email-validation', '', emailValidationCheckboxValue);

	return rules;
}


/**
 * This is the main function that builds the whole question section
 * @param id
 * @param name
 * @param type
 * @param attributes
 * @returns {string}
 */
function buildNewQuestionDiv(id, name, type, attributes)
{
	if(type === undefined)
		type = 'text';

	var questionDiv =  addNewQuestionHeader(id, name, type, attributes) ;

	if(readonlyQuestion)
		questionDiv += '<input type="hidden" name="readonlyType['+questionCount+']" value="1">';
	else
		questionDiv += '<input type="hidden" name="readonlyType['+questionCount+']" value="0">';

	for (var lang in languages) {
		questionDiv +=  addQuestionLanguageTab(lang, attributes);
	}

	questionDiv +=  addNewQuestionFooter();

	return questionDiv;
}

/**
 * Shows and hides fields as needed depending on the selection of the type
 * @param type
 */
function showAndHideAttributes(type)
{
	var questionFrame = type.closest( '.question-section' );
	var defaultInput = questionFrame.find('.question-default');
	questionFrame.find(' .question-default, .question-maxlength, .question-minimum, .question-maximum, .question-option, .question-defaultCheckbox, .question-label, .question-placeholder, .question-html, .question-required ').hide();
	switch(type.val()) {
		case 'checkbox':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			questionFrame.find('.question-defaultCheckbox ').show();
			break;
		case 'select':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			questionFrame.find('.question-option').show();
			break;
		case 'colour':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			defaultInput.find('input').attr('type', 'color');
			defaultInput.show();
			break;
		case 'date':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			defaultInput.find('input').attr('type', 'date');
			defaultInput.show();
			break;
		case 'email':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			questionFrame.find(' .question-maxlength').show();
			break;
		case 'text':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			defaultInput.find('input').attr('type', 'text');
			questionFrame.find(' .question-maxlength, .question-default').show();
			break;
		case 'number':
		case 'range':
			questionFrame.find(' .question-label, .question-placeholder, .question-required ').show();
			defaultInput.find('input').attr('type', 'number');
			questionFrame.find('.question-minimum, .question-default, .question-maximum ').show();
			break;
		case 'html':
			questionFrame.find(' .question-html ').show();
			questionFrame.find(' .question-required ').hide();
			break;
		default:
	}
}

/**
 *
 * @param id
 * @param name
 * @param type
 * @param attributes
 */
function addNewQuestion(id, name, type, attributes)
{
	var questionsDiv = $(".forms .questions");
	questionsDiv.prepend(buildNewQuestionDiv(id, name, type, attributes));

	var questionName = translate["question"]+' '+questionCount;

	if(name !== undefined)
		questionName = name;

	questionsDiv.find('.question-number').first().prepend(' <strong>'+ questionName + '</strong>');
	questionsDiv.find('.questionTypes').first().trigger('change');
	// questionsDiv.find('input[type="text"].questionTypes').first().trigger('change');

	questionsDiv.sortable({
		handle: ".question-header",
		connectWith: ".forms .question-section",
		axis: "y",

		stop: function(event, ui) {
		}
	});
}

/**
 *
 * @param data
 */
function prepareEditMode(data)
{
	//console.log(Object(data));
	var questionIds = Object.keys(data);
	//var questionsObjects = Object.values(data);


	$.each(questionIds.reverse(), function(questionIndex, questionId){
		var question = Object(data)[questionId];
		var attributes = [];


		attributes['required'] = question['required'];

		//var questionId = question['id'];
		var questionName = question['name'];
		var questionType = question['type'];

		var questionAttributes = question['attributes'];

		$.each(questionAttributes, function(attributeIndex, attribute){
			var attributeName = attribute['name'];
			var attributeValue = attribute['value'];
			var attributeType = attribute['type'];

			if (attributeType.length == 2)
			{
				if (attributes[attributeName] === undefined)
					attributes[attributeName] = [];
				attributes[attributeName][attributeType] = attributeValue;
			}
			else
			{
				if(attributeName == attributeValue)
				{
					if(attributes[attributeType] === undefined)
						attributes[attributeType] = [];

					attributes[attributeType].push(attributeValue);

				}
				else
				{
					attributes[attributeName] = attributeValue;
				}
			}
		});

		if(question['status'] == "readonly")
			readonlyQuestion = true;

		addNewQuestion(questionId, questionName, questionType, attributes);

		if(attributes['option'] !== undefined)
		{
			var questionNo = questionCount - 1;
			var options = attributes['option'];

			$.each(options , function( index, value ) {
				addOption($('.optionInput-'+questionNo), questionNo, value, attributes);
			});
		}

		readonlyQuestion = false;
		$('body').trigger('questions.loaded');
	});
}

/**
 *
 * @param dropdown
 */
function getQuestion(dropdown)
{
	var id = dropdown.find(":selected").val();
	if(id != '')
	{
		$(".loading_page").fadeIn("fast");
		$.ajax({
			url : '/manage/forms/:question/'+id,
			type: "GET",
			data: {
			}})
			.success (function(data){
				prepareEditMode(data);
				$(".loading_page").fadeOut("fast");
				dropdown.find(":selected").prop("selected", false);
				dropdown.trigger("chosen:updated");
				readonlyQuestion = false;
			});
	}
}

/**
 *
 * @param optionInput
 * @param questionNo
 * @param optionValue
 * @param attributes
 */
function addOption(optionInput, questionNo, optionValue, attributes)
{
	var isDisabled = '';

	if(readonlyQuestion)
		isDisabled = ' DISABLED ';


	if(optionValue === undefined)
		optionValue = optionInput.val();


	if(optionValue != '')
	{
		var newKeyInput = '<div ' +
			'class="input-group selectKeyValueGroup">' +

			'<input ' +
			'class="form-control selectKeyValue" ' +
			'READONLY ' +
			'value="'+optionValue+'" ' +
			'type="text"  ' +
			'name="question-option-keys['+questionNo+'][]" ' +
			'placeholder="' + translate["option-placeholder"] + '" ' +
			'title="' + translate["option-placeholder"] + '" ' +
			'maxlength="255">'+

			'<span class="input-group-btn">' +

			'<button ' +
			'class="btn btn-danger remove-value" ' +
			'title="' + translate['remove'] + '" '
			+ isDisabled +
			' type="button" ' +
			'data-id="' + questionNo + '" ' +
			'data-value="' + optionValue + '">' +

			'<i class="fa fa-minus"></i>' +

			'</button></span></div>';

		optionInput.closest('.question-attribute-option').append(newKeyInput);

		optionInput.val('');

		for (var lang in languages) {
			var newValueInput = '<div class="input-group" id="'+questionNo+'-'+optionValue.replace(/\s+/g, '')+'-'+lang+'">' +
				'<span class="input-group-addon">'+optionValue+'</span>' +
				'<input class="form-control" name="question-option[' + questionNo + '][' + lang + '][]" ';

			if(attributes !== undefined && attributes[optionValue] !== undefined && attributes[optionValue][lang] !== undefined)
				newValueInput += ' value="'+attributes[optionValue][lang]+'"';

			// Disabling the question attributes for predefined questions
			if(lang == '' && readonlyQuestion)
				newValueInput += ' READONLY ';

			newValueInput += ' type="text" id="question-option-'+optionValue.replace(/\s+/g, '') + questionNo + lang +
				'" placeholder="' + translate["option-placeholder"] +
				'" title="' + translate["option-placeholder"] + '" maxlength="255"></div>';

			optionInput.parents('.question-section').find('.question-attribute-option'+lang).append(newValueInput);
		}
	}
}

$(document).ready(function () {

	var body = $("body");

	body.on("click", ".add-value", function(){
		var questionNo = $(this).data('id');
		var optionInput = $(this).parent().parent().find('input');
		addOption(optionInput, questionNo);
	});

	body.on("click", ".remove-value", function(){
		var value = $(this).data('value').toString();
		var questionNo = $(this).data('id');
		$(this).closest('.selectKeyValueGroup').remove();
		$( "div[id^=" +questionNo+"-"+ value.replace(/\s+/g, '') + "]" ).remove();
	});

	body.on("click", ".remove-question", function(){
		$(this).parent().parent().remove();
	});

	// Question type change
	body.on("change", ".questionTypes", function(){
		showAndHideAttributes($(this));
	});

	// Adds a new question section
	$(".forms #add-question").on("click", function () {
		addNewQuestion();
	});

	// Adds a new predefined question section
	$(".forms  #knownQuestions ").on("change", function () {
		getQuestion($(this));
	});

	// Adds a new predefined question section
	$(".forms #predefinedQuestions").on("change", function () {
		readonlyQuestion = true;
		getQuestion($(this));
	});

	body.on('questions.loaded',function() {

		var mobileNumberQuestions = $(".question-attribute-sms-validation input[type='checkbox']");

		// Check any existing mobile number questions that required is checked
		mobileNumberQuestions.each(function() {
			checkRequiredOnMobileNumberQuestion($(this));
		});

		// Fire the function when the question is clicked
		mobileNumberQuestions.click(function() {
			checkRequiredOnMobileNumberQuestion($(this));
		});
	});
});

function checkRequiredOnMobileNumberQuestion(mobileNumberVerificationCheckbox)
{
	var requiredCheckbox = mobileNumberVerificationCheckbox.closest('.question-section').find('.question-attribute-required input[type=checkbox]');
	//Because disabled elements are not submitted, we set the value on this other input
	var requiredHiddenCheckbox = mobileNumberVerificationCheckbox.closest('.question-section').find('.question-attribute-required input[type=hidden]');

	if(mobileNumberVerificationCheckbox.is(':checked'))
	{
		requiredCheckbox.prop('checked', true).prop('disabled', true);
		requiredHiddenCheckbox.val(1);
	} else {
		requiredCheckbox.prop('disabled', false);
		requiredHiddenCheckbox.val(0);
	}
}
