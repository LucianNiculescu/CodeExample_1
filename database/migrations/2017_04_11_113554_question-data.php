<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\AirConnect\Question as QuestionModel;

class QuestionData extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {
			self::insertAgeQuestion();
			self::insertEmailQuestion();
			self::insertMarketingQuestion();
			self::insertNameQuestion();
			self::insertTermsQuestion();

		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		self::removeAgeQuestion();
		self::removeEmailQuestion();
		self::removeMarketingQuestion();
		self::removeNameQuestion();
		self::removeTermsQuestion();

	}



	private static function insertMarketingQuestion()
	{
		DB::connection('airconnect')
			->table('question')
			->insert([
				['portal' => 0, 'name' => 'Marketing', 'type' => 'checkbox', 'required' => '0', 'order' => '0', 'status' => 'active'],
			]);

		$id = DB::getPdo()->lastInsertId();

		if (Schema::connection('airconnect')->hasTable('question_attribute')) {
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					//Label
					['ids' => $id , 'name' => 'label', 'type' => 'en'		, 'value' => 'Marketing'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'es'		, 'value' => 'Márketing', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'de'		, 'value' => 'Marketing', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'fr'		, 'value' => 'Commercialisation', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'ar'		, 'value' => 'التسويق', 'status' => 'active'],
					//Placeholder
					['ids' => $id , 'name' => 'placeholder', 'type' => 'en'		, 'value' => 'I want to receive marketing information'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'es'		, 'value' => 'Deseo recibir información de marketing', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'de'		, 'value' => 'Ich möchte Marketinginformationen erhalten', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'fr'		, 'value' => 'Je souhaite recevoir des informations marketing', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'ar'		, 'value' => 'أريد الحصول على المعلومات التسويقية', 'status' => 'active'],
				]);
		}
	}

	private static function insertTermsQuestion()
	{
		DB::connection('airconnect')
			->table('question')
			->insert([
				['portal' => 0, 'name' => 'Terms & Conditions', 'type' => 'checkbox', 'required' => '1', 'order' => '0', 'status' => 'active'],
			]);

		$id = DB::getPdo()->lastInsertId();

		if (Schema::connection('airconnect')->hasTable('question_attribute')) {
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					//Label
					['ids' => $id , 'name' => 'label', 'type' => 'en'		, 'value' => 'I accept the <a href="/portal/guest/terms" data-action="terms">terms and conditions</a>'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'es'		, 'value' => '<a href="/portal/guest/terms" data-action="terms">términos y Condiciones</a>', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'de'		, 'value' => '<a href="/portal/guest/terms" data-action="terms">Geschäftsbedingungen</a>', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'fr'		, 'value' => '<a href="/portal/guest/terms" data-action="terms">Termes et conditions</a>', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'ar'		, 'value' => '<a href="/portal/guest/terms" data-action="terms">الشروط والأحكام</a>', 'status' => 'active'],
					//Placeholder
					['ids' => $id , 'name' => 'placeholder', 'type' => 'en'		, 'value' => 'Please read and accept the terms and conditions'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'es'		, 'value' => 'Por favor, lea y acepte los términos y condiciones', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'de'		, 'value' => 'Bitte lesen und akzeptieren Sie die Allgemeinen Geschäftsbedingungen', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'fr'		, 'value' => 'S\'il vous plaît lire et accepter les termes et conditions', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'ar'		, 'value' => 'الرجاء قراءة وقبول الشروط والأحكام', 'status' => 'active'],
				]);
		}
	}

	private static function insertNameQuestion()
	{
		DB::connection('airconnect')
			->table('question')
			->insert([
				['portal' => 0, 'name' => 'Name', 'type' => 'text', 'required' => '0', 'order' => '0', 'status' => 'active'],
			]);

		$id = DB::getPdo()->lastInsertId();

		if (Schema::connection('airconnect')->hasTable('question_attribute')) {
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					//Label
					['ids' => $id , 'name' => 'label', 'type' => 'en'		, 'value' => 'Name'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'es'		, 'value' => 'Nombre', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'de'		, 'value' => 'Name', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'fr'		, 'value' => 'Nom', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'ar'		, 'value' => 'الإسم', 'status' => 'active'],
					//Placeholder
					['ids' => $id , 'name' => 'placeholder', 'type' => 'en'		, 'value' => 'Please enter your name'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'es'		, 'value' => 'Por favor, escriba su nombre', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'de'		, 'value' => 'Bitte geben Sie Ihren Namen ein', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'fr'		, 'value' => 'S\'il vous plaît entrez votre nom', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'ar'		, 'value' => 'الرجاء إدخال إسمك', 'status' => 'active'],
				]);
		}
	}

	private static function insertEmailQuestion()
	{
		DB::connection('airconnect')
			->table('question')
			->insert([
				['portal' => 0, 'name' => 'Email', 'type' => 'email', 'required' => '0', 'order' => '0', 'status' => 'active'],
			]);

		$id = DB::getPdo()->lastInsertId();

		if (Schema::connection('airconnect')->hasTable('question_attribute')) {
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					//Label
					['ids' => $id , 'name' => 'label', 'type' => 'en'		, 'value' => 'Email'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'es'		, 'value' => 'Email', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'de'		, 'value' => 'Email', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'fr'		, 'value' => 'Email', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'ar'		, 'value' => 'البريد الإلكتروني', 'status' => 'active'],
					//Placeholder
					['ids' => $id , 'name' => 'placeholder', 'type' => 'en'		, 'value' => 'Please enter your email'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'es'		, 'value' => 'Por favor introduzca su correo electrónico', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'de'		, 'value' => 'Bitte geben Sie ihre E-Mail-Adresse ein', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'fr'		, 'value' => 'S\'il vous plaît entrer votre e-mail', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'ar'		, 'value' => 'الرجاء إدخال البريد الإلكتروني الخاص بك', 'status' => 'active'],
				]);
		}
	}



	private static function insertAgeQuestion()
	{
		DB::connection('airconnect')
			->table('question')
			->insert([
				['portal' => 0, 'name' => 'Age', 'type' => 'select', 'required' => '0', 'order' => '0', 'status' => 'active'],
			]);

		$id = DB::getPdo()->lastInsertId();

		if (Schema::connection('airconnect')->hasTable('question_attribute')) {
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					//Label
					['ids' => $id , 'name' => 'label', 'type' => 'en'		, 'value' => 'Age'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'es'		, 'value' => 'Años', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'de'		, 'value' => 'Alter', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'fr'		, 'value' => 'Âge', 'status' => 'active'],
					['ids' => $id , 'name' => 'label', 'type' => 'ar'		, 'value' => 'العمر', 'status' => 'active'],
					//Placeholder
					['ids' => $id , 'name' => 'placeholder', 'type' => 'en'		, 'value' => 'Please select your age'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'es'		, 'value' => 'Por favor seleccione su edad', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'de'		, 'value' => 'Bitte wählen Sie Ihr Alter aus', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'fr'		, 'value' => 'S\'il vous plaît sélectionnez votre âge', 'status' => 'active'],
					['ids' => $id , 'name' => 'placeholder', 'type' => 'ar'		, 'value' => 'الرجاء تحديد عمرك', 'status' => 'active'],
				]);
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					['ids' => $id , 'name' => 'Under 18', 'type' => 'option'	, 'value' => 'Under 18'			, 'status' => 'active'],
					['ids' => $id , 'name' => 'Under 18', 'type' => 'en'		, 'value' => 'Under 18 years'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'Under 18', 'type' => 'es'		, 'value' => 'Menores de 18 años', 'status' => 'active'],
					['ids' => $id , 'name' => 'Under 18', 'type' => 'de'		, 'value' => 'Unter 18 Jahren', 'status' => 'active'],
					['ids' => $id , 'name' => 'Under 18', 'type' => 'fr'		, 'value' => 'Moins de 18 ans', 'status' => 'active'],
					['ids' => $id , 'name' => 'Under 18', 'type' => 'ar'		, 'value' => 'تحت 18 عاما', 'status' => 'active'],
				]);
			DB::connection('airconnect')
				->table('question_attribute')
				->insert([
					['ids' => $id , 'name' => '18 - 21', 'type' => 'option'	, 'value' => '18 - 21'			, 'status' => 'active'],
					['ids' => $id , 'name' => '18 - 21', 'type' => 'en'		, 'value' => 'Between 18 and 21'	, 'status' => 'active'],
					['ids' => $id , 'name' => '18 - 21', 'type' => 'es'		, 'value' => 'Entre el 18 y el 21', 'status' => 'active'],
					['ids' => $id , 'name' => '18 - 21', 'type' => 'de'		, 'value' => 'Zwischen 18 und 21', 'status' => 'active'],
					['ids' => $id , 'name' => '18 - 21', 'type' => 'fr'		, 'value' => 'Entre 18 et 21', 'status' => 'active'],
					['ids' => $id , 'name' => '18 - 21', 'type' => 'ar'		, 'value' => 'بين 18 و 21', 'status' => 'active'],

					['ids' => $id , 'name' => '22 - 35', 'type' => 'option'	, 'value' => '22 - 35'			, 'status' => 'active'],
					['ids' => $id , 'name' => '22 - 35', 'type' => 'en'		, 'value' => 'Between 22 and 35'	, 'status' => 'active'],
					['ids' => $id , 'name' => '22 - 35', 'type' => 'es'		, 'value' => 'Entre el 22 y el 35', 'status' => 'active'],
					['ids' => $id , 'name' => '22 - 35', 'type' => 'de'		, 'value' => 'Zwischen 22 und 35', 'status' => 'active'],
					['ids' => $id , 'name' => '22 - 35', 'type' => 'fr'		, 'value' => 'Entre 22 et 35', 'status' => 'active'],
					['ids' => $id , 'name' => '22 - 35', 'type' => 'ar'		, 'value' => 'بين 22 و 35', 'status' => 'active'],

					['ids' => $id , 'name' => '36 - 50', 'type' => 'option'	, 'value' => '36 - 50'			, 'status' => 'active'],
					['ids' => $id , 'name' => '36 - 50', 'type' => 'en'		, 'value' => 'Between 36 and 50'	, 'status' => 'active'],
					['ids' => $id , 'name' => '36 - 50', 'type' => 'es'		, 'value' => 'Entre el 36 y el 50', 'status' => 'active'],
					['ids' => $id , 'name' => '36 - 50', 'type' => 'de'		, 'value' => 'Zwischen 36 und 50', 'status' => 'active'],
					['ids' => $id , 'name' => '36 - 50', 'type' => 'fr'		, 'value' => 'Entre 36 et 50', 'status' => 'active'],
					['ids' => $id , 'name' => '36 - 50', 'type' => 'ar'		, 'value' => 'بين 36 و 50', 'status' => 'active'],

					['ids' => $id , 'name' => 'Over 50', 'type' => 'option'	, 'value' => 'Over 50'			, 'status' => 'active'],
					['ids' => $id , 'name' => 'Over 50', 'type' => 'en'		, 'value' => 'Over 50 years'	, 'status' => 'active'],
					['ids' => $id , 'name' => 'Over 50', 'type' => 'es'		, 'value' => 'Más de 50 años', 'status' => 'active'],
					['ids' => $id , 'name' => 'Over 50', 'type' => 'de'		, 'value' => 'Über 50 Jahre', 'status' => 'active'],
					['ids' => $id , 'name' => 'Over 50', 'type' => 'fr'		, 'value' => 'Plus de 50 ans', 'status' => 'active'],
					['ids' => $id , 'name' => 'Over 50', 'type' => 'ar'		, 'value' => 'أكثر من 50 عاما', 'status' => 'active'],

				]);
		}
	}


	private static function deleteQuestion($question)
	{
		if(!is_null($question))
		{
			$id = $question->id;
			$question = new QuestionModel();
			$question::find($id)->delete();
		}
	}


	private static function removeAgeQuestion()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {

			$existingQuestion = QuestionModel::where('name',  'age')
				->where('portal',  '0')->first();

			self::deleteQuestion($existingQuestion);
		}
	}

	private static function removeNameQuestion()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {

			$existingQuestion = QuestionModel::where('name',  'name')
				->where('portal',  '0')->first();

			self::deleteQuestion($existingQuestion);
		}
	}

	private static function removeEmailQuestion()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {

			$existingQuestion = QuestionModel::where('name',  'email')
				->where('portal',  '0')->first();

			self::deleteQuestion($existingQuestion);
		}
	}

	private static function removeMarketingQuestion()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {

			$existingQuestion = QuestionModel::where('name',  'marketing')
				->where('portal',  '0')->first();

			self::deleteQuestion($existingQuestion);
		}
	}

	private static function removeTermsQuestion()
	{
		if (Schema::connection('airconnect')->hasTable('question')) {

			$existingQuestion = QuestionModel::where('name',  'Terms & Conditions')
				->where('portal',  '0')->first();

			self::deleteQuestion($existingQuestion);
		}
	}
}
