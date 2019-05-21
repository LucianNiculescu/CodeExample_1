(function( factory ) {
	if ( typeof define === "function" && define.amd ) {
		define( ["jquery", "../jquery.validate"], factory );
	} else if (typeof module === "object" && module.exports) {
		module.exports = factory( require( "jquery" ) );
	} else {
		factory( jQuery );
	}
}(function( $ ) {

	if(LANG == 'ar')
	{
		/*
		 * Translated default messages for the jQuery validation plugin.
		 * Locale: AR (Arabic; العربية)
		 */
		$.extend( $.validator.messages, {
			required: "هذا الحقل إلزامي",
			remote: "يرجى تصحيح هذا الحقل للمتابعة",
			email: "رجاء إدخال عنوان بريد إلكتروني صحيح",
			url: "رجاء إدخال عنوان موقع إلكتروني صحيح",
			date: "رجاء إدخال تاريخ صحيح",
			dateISO: "رجاء إدخال تاريخ صحيح (ISO)",
			number: "رجاء إدخال عدد بطريقة صحيحة",
			digits: "رجاء إدخال أرقام فقط",
			creditcard: "رجاء إدخال رقم بطاقة ائتمان صحيح",
			equalTo: "رجاء إدخال نفس القيمة",
			extension: "رجاء إدخال ملف بامتداد موافق عليه",
			maxlength: $.validator.format( "الحد الأقصى لعدد الحروف هو {0}" ),
			minlength: $.validator.format( "الحد الأدنى لعدد الحروف هو {0}" ),
			rangelength: $.validator.format( "عدد الحروف يجب أن يكون بين {0} و {1}" ),
			range: $.validator.format( "رجاء إدخال عدد قيمته بين {0} و {1}" ),
			max: $.validator.format( "رجاء إدخال عدد أقل من أو يساوي {0}" ),
			min: $.validator.format( "رجاء إدخال عدد أكبر من أو يساوي {0}" )
		} );
	}
	else if(LANG == 'de')
	{
		/*
		 * Translated default messages for the jQuery validation plugin.
		 * Locale: DE (German, Deutsch)
		 */
		$.extend( $.validator.messages, {
			required: "Dieses Feld ist ein Pflichtfeld.",
			maxlength: $.validator.format( "Geben Sie bitte maximal {0} Zeichen ein." ),
			minlength: $.validator.format( "Geben Sie bitte mindestens {0} Zeichen ein." ),
			rangelength: $.validator.format( "Geben Sie bitte mindestens {0} und maximal {1} Zeichen ein." ),
			email: "Geben Sie bitte eine gültige E-Mail Adresse ein.",
			url: "Geben Sie bitte eine gültige URL ein.",
			date: "Bitte geben Sie ein gültiges Datum ein.",
			number: "Geben Sie bitte eine Nummer ein.",
			digits: "Geben Sie bitte nur Ziffern ein.",
			equalTo: "Bitte denselben Wert wiederholen.",
			range: $.validator.format( "Geben Sie bitte einen Wert zwischen {0} und {1} ein." ),
			max: $.validator.format( "Geben Sie bitte einen Wert kleiner oder gleich {0} ein." ),
			min: $.validator.format( "Geben Sie bitte einen Wert größer oder gleich {0} ein." ),
			creditcard: "Geben Sie bitte eine gültige Kreditkarten-Nummer ein."
		} );
	}
	else if(LANG == 'es')
	{
		$.extend( $.validator.messages, {
			required: "Este campo es obligatorio.",
			remote: "Por favor, rellena este campo.",
			email: "Por favor, escribe una dirección de correo válida.",
			url: "Por favor, escribe una URL válida.",
			date: "Por favor, escribe una fecha válida.",
			dateISO: "Por favor, escribe una fecha (ISO) válida.",
			number: "Por favor, escribe un número válido.",
			digits: "Por favor, escribe sólo dígitos.",
			creditcard: "Por favor, escribe un número de tarjeta válido.",
			equalTo: "Por favor, escribe el mismo valor de nuevo.",
			extension: "Por favor, escribe un valor con una extensión aceptada.",
			maxlength: $.validator.format( "Por favor, no escribas más de {0} caracteres." ),
			minlength: $.validator.format( "Por favor, no escribas menos de {0} caracteres." ),
			rangelength: $.validator.format( "Por favor, escribe un valor entre {0} y {1} caracteres." ),
			range: $.validator.format( "Por favor, escribe un valor entre {0} y {1}." ),
			max: $.validator.format( "Por favor, escribe un valor menor o igual a {0}." ),
			min: $.validator.format( "Por favor, escribe un valor mayor o igual a {0}." ),
			nifES: "Por favor, escribe un NIF válido.",
			nieES: "Por favor, escribe un NIE válido.",
			cifES: "Por favor, escribe un CIF válido."
		} );
	}
	else if(LANG == 'fr')
	{
		/*
		 * Translated default messages for the jQuery validation plugin.
		 * Locale: FR (French; français)
		 */
		$.extend( $.validator.messages, {
			required: "Ce champ est obligatoire.",
			remote: "Veuillez corriger ce champ.",
			email: "Veuillez fournir une adresse électronique valide.",
			url: "Veuillez fournir une adresse URL valide.",
			date: "Veuillez fournir une date valide.",
			dateISO: "Veuillez fournir une date valide (ISO).",
			number: "Veuillez fournir un numéro valide.",
			digits: "Veuillez fournir seulement des chiffres.",
			creditcard: "Veuillez fournir un numéro de carte de crédit valide.",
			equalTo: "Veuillez fournir encore la même valeur.",
			extension: "Veuillez fournir une valeur avec une extension valide.",
			maxlength: $.validator.format( "Veuillez fournir au plus {0} caractères." ),
			minlength: $.validator.format( "Veuillez fournir au moins {0} caractères." ),
			rangelength: $.validator.format( "Veuillez fournir une valeur qui contient entre {0} et {1} caractères." ),
			range: $.validator.format( "Veuillez fournir une valeur entre {0} et {1}." ),
			max: $.validator.format( "Veuillez fournir une valeur inférieure ou égale à {0}." ),
			min: $.validator.format( "Veuillez fournir une valeur supérieure ou égale à {0}." ),
			maxWords: $.validator.format( "Veuillez fournir au plus {0} mots." ),
			minWords: $.validator.format( "Veuillez fournir au moins {0} mots." ),
			rangeWords: $.validator.format( "Veuillez fournir entre {0} et {1} mots." ),
			letterswithbasicpunc: "Veuillez fournir seulement des lettres et des signes de ponctuation.",
			alphanumeric: "Veuillez fournir seulement des lettres, nombres, espaces et soulignages.",
			lettersonly: "Veuillez fournir seulement des lettres.",
			nowhitespace: "Veuillez ne pas inscrire d'espaces blancs.",
			ziprange: "Veuillez fournir un code postal entre 902xx-xxxx et 905-xx-xxxx.",
			integer: "Veuillez fournir un nombre non décimal qui est positif ou négatif.",
			vinUS: "Veuillez fournir un numéro d'identification du véhicule (VIN).",
			dateITA: "Veuillez fournir une date valide.",
			time: "Veuillez fournir une heure valide entre 00:00 et 23:59.",
			phoneUS: "Veuillez fournir un numéro de téléphone valide.",
			phoneUK: "Veuillez fournir un numéro de téléphone valide.",
			mobileUK: "Veuillez fournir un numéro de téléphone mobile valide.",
			strippedminlength: $.validator.format( "Veuillez fournir au moins {0} caractères." ),
			email2: "Veuillez fournir une adresse électronique valide.",
			url2: "Veuillez fournir une adresse URL valide.",
			creditcardtypes: "Veuillez fournir un numéro de carte de crédit valide.",
			ipv4: "Veuillez fournir une adresse IP v4 valide.",
			ipv6: "Veuillez fournir une adresse IP v6 valide.",
			require_from_group: "Veuillez fournir au moins {0} de ces champs.",
			nifES: "Veuillez fournir un numéro NIF valide.",
			nieES: "Veuillez fournir un numéro NIE valide.",
			cifES: "Veuillez fournir un numéro CIF valide.",
			postalCodeCA: "Veuillez fournir un code postal valide."
		} );
	}


	return $;
}));