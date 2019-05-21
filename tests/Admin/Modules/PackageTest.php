<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Admin\Modules\Packages\SetupViewData;
use App\Models\AirConnect\Package;
use App\Models\AirConnect\Transaction;
use App\Admin\Modules\Packages\Events\PackageUpdated;
use App\Admin\Modules\Packages\Listeners\UpdateActiveTransactionsWithPackage;

/**
 * Class PackageTest
 *
 * Test intricates of storing and updating packages
 *
 * @TODO Get AJAX test for updating status to work
 */
class PackageTest extends TestCase
{
	use DatabaseTransactions;

	/**
	 * Vars to use in the test initialized in setup()
	 *
	 * @var array
	 */
	protected $testVars;

	/**
	 * Setup the test
	 */
	public function setup()
	{
		parent::setup();

		// Get a site to work on which we know has packages
		$package = Package::first();
		$site 	 = $package->site()->first();

		// Get a package to work on which has transactions
		$transaction			 = Transaction::first();
		$packageWithTransactions = $transaction->package;

		$this->testVars = [
			'user' 		=> new \App\Models\User(['name' => 'John Doe']),
			'site'		=> $site,
			'package'	=> $package,
			'package_with_transactions' => $packageWithTransactions,
			// Spoof the session var to select the site
			'session' 	=> [
				'admin' => [
					'site' => [
						'loggedin' => $site->id
					]
				]
			]
		];
	}

    /**
     * Test the index route loads without error, and is passed an
	 * array of packages to display in the datatable
     *
     * @return void
     */
    public function testIndex()
    {
    	// Test seeing the title on the page
    	$this->actingAs($this->testVars['user'])
				->withSession($this->testVars['session'])
				->visit( route('manage.packages.index') )
				->see('Packages');

    	// Grab the datatable rows for the Site's packages and format as the controller would
    	$datatableRows =  \App\Admin\Modules\Packages\Datatable::getPackagesDatatable(true, $this->testVars['site']->id);
    	$datatableFormattedArray = SetupViewData::formatRowsForDatatable($datatableRows);

		// Test the view is passed the formatted array in the rows key
    	$this->assertViewHas('rows', $datatableFormattedArray);
    }

	/**
	 * Test the create route loads without error
	 *
	 * @return void
	 */
    public function testCreate()
	{
		// Test seeing the title on the page
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit( route('manage.packages.create') )
			->see('Create Package');
	}

	/**
	 * Test the edit route loads without error
	 *
	 * @return void
	 */
	public function testEdit()
	{
		// Test seeing the title on the page
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit(route('manage.packages.edit', $this->testVars['package']))
			->see('Edit Packages');
	}

	/**
	 * Test creating a new package
	 *
	 * @return void
	 */
	public function testStore()
	{
		$uniquePackageName = uniqid('New Package Name');

		// Store a new package
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit( route('manage.packages.create') )
			->type($uniquePackageName, 'name')
			->type('0', 'download')
			->select('facebook', 'type')
			->press('submitButton');

		$package = Package::where('name', $uniquePackageName)->firstOrFail();
		$this->seePageIs( route('manage.packages.edit', [$package]) );

		// Verify the package has been stored in the database
		$this->seeInDatabase('package', [
			'site' => $this->testVars['site']->id,
			'type' => 'facebook',
			'name' => $uniquePackageName
		]);

		// Verify key package attributes have been stored in the database
		$this->seeInDatabase('package_attribute', [
			'ids'   => $package->id,
			'name'  => 'downstream',
			'type'  => 'radreply',
			'value' => 0
		]);
		$this->seeInDatabase('package_attribute', [
			'ids'   => $package->id,
			'name'  => 'upstream',
			'type'  => 'radreply',
			'value' => 0
		]);
		$this->seeInDatabase('package_attribute', [
			'ids'   => $package->id,
			'name'  => 'duration',
			'type'  => 'package',
			'value' => 2592000
		]);

		// Attribute 'Idle-Timeout' should have a default value
		$this->seeInDatabase('package_attribute', [
			'ids'   => $package->id,
			'name'  => 'Idle-Timeout',
			'type'  => 'radreply',
			'value' => 1200
		]);
	}

	/**
	 * Test updating a package
	 *
	 * @return void
	 */
	public function testUpdate()
	{
		$uniquePackageName = uniqid('Updating Name');

		// Update the package
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit( route('manage.packages.edit', $this->testVars['package']) )
			->type($uniquePackageName, 'name')
			->type('0', 'download')
			->press('submitButton')
			->seePageIs( route('manage.packages.edit', [$this->testVars['package']]) );

		// Verify the package has been stored in the database
		$this->seeInDatabase('package', [
			'id'   => $this->testVars['package']->id,
			'site' => $this->testVars['site']->id,
			'name' => $uniquePackageName
		]);
	}

	/**
	 * Test server side validation by entering non numeric
	 * values into fields which expect numeric
	 *
	 * @return void
	 */
	public function testServerSideValidation()
	{
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit('manage/packages/create')
			->type('string', 'duration')
			->type('string', 'cost')
			->press('submitButton')
			->seePageIs('/manage/packages/create')
			->see(trans('validation.numeric'));
	}

	/**
	 * Test seeing validation error when attempting to create more than
	 * one free package on the site
	 *
	 * @return void
	 */
	public function testCreatingDuplicateFreePackage()
	{
		// Add a free package to the site
		$this->testVars['site']->packages()->create([
			'name' => uniqid('Test Free Package'),
			'type' => 'email',
			'cost' => 0,
			'status' => 'active'
		]);

		$uniquePackageName = uniqid('New Package Name');

		// Store a new package
		$this->actingAs($this->testVars['user'])
			->withSession($this->testVars['session'])
			->visit( route('manage.packages.create') )
			->type($uniquePackageName, 'name')
			->type('0', 'download')
			->select('email', 'type')
			->press('submitButton')
			->seePageIs( route('manage.packages.create') )
			->see(trans('admin.packages-one-free-per-site'));
	}

	/**
	 * Test firing the event for updating a package which in turn
	 * updates all active transactions attributes
	 *
	 * @return void
	 */
	public function testUpdatingPackageTransactions()
	{
		$listener = new UpdateActiveTransactionsWithPackage;

		// Create an event for package updated but signify not to update transactions
		$event = new PackageUpdated($this->testVars['package_with_transactions'], false);
		$result = $listener->handle($event);
		$this->assertEquals(false, $result);

		// Get the first gateway ID for the current site
		$gatewayId = $this->testVars['package_with_transactions']->parentSite->gateways->first()->id;

		// Create an event for package updated and signify to update transactions
		$event = new PackageUpdated($this->testVars['package_with_transactions'], true, $gatewayId);

		// Get a count of the number of transactions we will be updating
		$count = Transaction::where([
			'package_id' => $this->testVars['package_with_transactions']->id,
			'status' => 'Completed'
		])->count();
		$result = $listener->handle($event);

		$this->assertEquals($count, $listener->transactionsUpdated);
		$this->assertEquals(true, $result);
	}
}
