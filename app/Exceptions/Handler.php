<?php
namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\Debug\Exception\FlattenException;

class Handler extends ExceptionHandler
{
    // @var int
    protected $randomCode;

    //A list of admin urls that will redirect the user to /admin
	protected $adminUrls = [
		'estate',
		'dashboard',
		'online-now',
		'manage',
		'reports',
		'networking',
		'system',
		'help',
		'messages',
		'gateways',
		'migration',
		'roles-and-permissions',
		'sites',
		'translations',
		'injectionjet-templates',
		'users'
	];

    // A list of the exception types that should not be reported.
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];


    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
		$e = FlattenException::create($exception);
		$statusCode = $e->getStatusCode();

		//Do not slack/log when there is a TokenMismatchException Error
		if (!$exception instanceof TokenMismatchException || !in_array($statusCode, [418])) {
			$this->randomCode = Errors\Logic::reportError($exception);
			parent::report($exception);
		}
    }


    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {

		// If we are in debug mode, lets show the default error
		if(config('app.debug'))
			return parent::render($request, $e);

		// If we have a location, use that
		if( method_exists( $e, 'getHeaders' ) && isset($e->getHeaders()['Location']))
			//return \Response::make( $e->getMessage(), $e->getStatusCode() )->header( 'Location', $e->getHeaders()['Location'] .'?ERROR=' .$e->getMessage() );
			return redirect($e->getHeaders()['Location'] .'?ERROR=' .$e->getMessage());

		if ($e instanceof TokenMismatchException) {

			//Check if the url is from admin (because we don't have the session, we use the custom url)
			foreach($this->adminUrls as $adminUrl) {
				if(strpos(url()->current(), $adminUrl))
					return redirect('/admin/');
			}
			//Portal will redirect to this url
            return redirect(config('app.abort_url'));
		}

		$exception = FlattenException::create($e);
        $statusCode = $exception->getStatusCode();

        if (in_array($statusCode, [401, 404, 500, 503]))
            return redirect("error/$statusCode/$this->randomCode");

        //All other HttpExceptions are NOT redirected (only if the user is not logged in)
        return Errors\Logic::getDefaultError($exception, $this->randomCode);
    }


    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest(route('login'));
    }
}